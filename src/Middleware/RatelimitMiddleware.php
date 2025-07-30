<?php

namespace Kyzegs\Laracord\Middleware;

use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Sleep;
use Kyzegs\Laracord\Route;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RatelimitMiddleware
{
    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler): PromiseInterface {
            $route = $options['laracord_route'] ?? null;
            if (! $route instanceof Route) {
                return $handler($request, $options);
            }

            return $this->process($handler, $request, $options, $route);
        };
    }

    private function process(callable $handler, RequestInterface $request, array $options, Route $route): PromiseInterface
    {
        $bucket = $route->getBucket();
        $bucketHash = $route->getBucketHash()->get();
        $ratelimit = $bucket->get();
        $lock = $bucket->lock();
        $tries = 0;

        while (true) {
            $lock->block(PHP_INT_MAX);
            try {
                if ($ratelimit->getRemaining() === 0) {
                    $sleepFor = $ratelimit->getResetAfter();
                    if ($sleepFor > 0) {
                        Log::debug(sprintf('Sleeping rate limit bucket %s for %.2f seconds.', $bucketHash ?? $route->getKey(), $sleepFor));
                        Sleep::sleep($sleepFor);
                    }
                }

                /** @var ResponseInterface $response */
                $response = $handler($request, $options)->wait();

                $body = (string) $response->getBody();
                $response->getBody()->rewind();
                $data = json_decode($body, true);

                $discordHash = $response->getHeaderLine('X-Ratelimit-Bucket');
                if ($discordHash !== '') {
                    if ($bucketHash !== $discordHash) {
                        if ($bucketHash !== null) {
                            Log::debug(sprintf('A route (%s) has changed hashes: %s -> %s.', $route->getKey(), $bucketHash, $discordHash));
                            $bucket->forget();
                        } elseif ($route->getBucketHash()->missing()) {
                            Log::debug(sprintf('%s has found its initial rate limit bucket hash (%s).', $route->getKey(), $discordHash));
                        }

                        $route->getBucketHash()->put($discordHash);
                        $bucket->put($ratelimit);
                    }
                }

                if ($response->hasHeader('X-Ratelimit-Remaining') && $response->getStatusCode() !== 429) {
                    $bucket->put($ratelimit->update($response));

                    if ($ratelimit->getRemaining() === 0) {
                        Log::debug(sprintf('A rate limit bucket (%s) has been exhausted. Pre-emptively rate limiting...', $discordHash ?: $route->getKey()));
                    }
                }

                if ($response->getStatusCode() === 429) {

                    if ($ratelimit->getRemaining() > 0) {
                        Log::debug(sprintf('%s %s received a 429 despite having %d remaining requests. This is a sub-ratelimit.', $route->getMethod(), $route->getUrl(), $ratelimit->getRemaining()));
                    }

                    $data = is_array($data) ? $data : [];
                    $retryAfter = (float) ($data['retry_after'] ?? 0);
                    if ($retryAfter > 0) {
                        $ratelimit->retry($retryAfter);
                        $bucket->put($ratelimit);
                    }

                    Log::warning(sprintf('We are being rate limited. %s %s responded with 429. Retrying in %.2f seconds.', $route->getMethod(), $route->getUrl(), $retryAfter));
                    Log::debug(sprintf('Rate limit is being handled by bucket hash %s with %s major parameters.', $bucketHash, $route->getMajorParameters()));

                    Sleep::sleep($retryAfter);
                    $ratelimit->reset();
                    $lock->release();
                    $tries++;

                    if ($tries >= 3) {
                        return Create::promiseFor($response);
                    }

                    continue;
                }

                return Create::promiseFor($response);
            } finally {
                $lock->release();
            }
        }
    }
}
