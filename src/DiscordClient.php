<?php

declare(strict_types=1);

namespace Kyzegs\Laracord;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\RequestOptions;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Sleep;
use Kyzegs\GuzzleRateLimitMiddleware\Exception\InvalidRequestLimitExceededException;
use Kyzegs\GuzzleRateLimitMiddleware\Exception\RateLimitDelayExceededException;
use Kyzegs\GuzzleRateLimitMiddleware\Exception\RateLimitExceededException;
use Kyzegs\Laracord\Endpoints\EndpointCatalog;
use Kyzegs\Laracord\Enums\AuthenticationRequirement;
use Kyzegs\Laracord\Exceptions\DiscordAuthenticationException;
use Kyzegs\Laracord\Exceptions\DiscordForbiddenException;
use Kyzegs\Laracord\Exceptions\DiscordHttpException;
use Kyzegs\Laracord\Exceptions\DiscordInvalidRequestLimitException;
use Kyzegs\Laracord\Exceptions\DiscordNotFoundException;
use Kyzegs\Laracord\Exceptions\DiscordRateLimitException;
use Kyzegs\Laracord\Exceptions\DiscordServerException;
use Kyzegs\Laracord\Exceptions\DiscordTransportException;
use Kyzegs\Laracord\Exceptions\MissingAuthenticationException;
use Kyzegs\Laracord\Http\DiscordRequest;
use Kyzegs\Laracord\Http\DiscordResponse;
use Kyzegs\Laracord\Resources\ResourceClient;
use Kyzegs\Laracord\ValueObjects\AuditLogReason;
use Kyzegs\Laracord\ValueObjects\Authentication;
use Kyzegs\Laracord\ValueObjects\OAuthAccessToken;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final readonly class DiscordClient
{
    public function __construct(
        private ClientFactory $clientFactory,
        private ClientInterface $client,
        private Authentication $authentication,
        private Repository $repository,
    ) {}

    public function asBot(?string $token = null): self
    {
        return $this->clientFactory->make(Authentication::bot($token ?? (string) $this->repository->get('laracord.bot_token', '')));
    }

    public function asUser(string|OAuthAccessToken $token): self
    {
        return $this->clientFactory->make(Authentication::bearer($token));
    }

    public function withoutAuthentication(): self
    {
        return $this->clientFactory->make(Authentication::none());
    }

    public function resource(string $name): ResourceClient
    {
        return new ResourceClient($this, $name);
    }

    /** @param array<int, mixed> $arguments */
    public function __call(string $name, array $arguments): ResourceClient
    {
        if ($arguments === [] && EndpointCatalog::hasResource($name)) {
            return $this->resource($name);
        }

        throw new \BadMethodCallException('Unknown Discord resource '.$name.'.');
    }

    public function send(DiscordRequest $discordRequest): DiscordResponse
    {
        $header = $this->authentication->header();
        if ($discordRequest->authentication === AuthenticationRequirement::REQUIRED && $header === null) {
            throw new MissingAuthenticationException('Discord endpoint requires bot or bearer authentication.');
        }

        $options = [RequestOptions::HTTP_ERRORS => false];
        if ($discordRequest->authentication !== AuthenticationRequirement::NONE && $header !== null) {
            $options[RequestOptions::HEADERS]['Authorization'] = $header;
        }

        if ($discordRequest->reason instanceof AuditLogReason) {
            $options[RequestOptions::HEADERS]['X-Audit-Log-Reason'] = rawurlencode((string) $discordRequest->reason);
        }

        $path = ltrim($discordRequest->resolvedPath(), '/').$this->queryString($discordRequest->query);
        $body = $discordRequest->bodyArray();
        if ($discordRequest->files !== []) {
            $multipart = [];
            if ($body !== null) {
                $multipart[] = ['name' => 'payload_json', 'contents' => json_encode($body, JSON_THROW_ON_ERROR)];
            }

            foreach ($discordRequest->files as $index => $file) {
                $part = [
                    'name' => (string) ($file['name'] ?? 'files['.$index.']'),
                    'contents' => $file['contents'],
                ];
                if (isset($file['filename'])) {
                    $part['filename'] = $file['filename'];
                }

                if (isset($file['content_type'])) {
                    $part['headers'] = ['Content-Type' => $file['content_type']];
                }

                $multipart[] = $part;
            }

            $options[RequestOptions::MULTIPART] = $multipart;
        } elseif ($body !== null) {
            $options[$discordRequest->form ? RequestOptions::FORM_PARAMS : RequestOptions::JSON] = $body;
        }

        $attempts = max(1, (int) $this->repository->get('laracord.http.server_retries', 5));
        $response = null;
        for ($attempt = 0; $attempt < $attempts; $attempt++) {
            try {
                $response = $this->client->request($discordRequest->method->value, $path, $options);
            } catch (\Throwable $exception) {
                if ($exception instanceof InvalidRequestLimitExceededException) {
                    throw new DiscordInvalidRequestLimitException($exception->retryAfter, previous: $exception);
                }

                if ($exception instanceof RateLimitDelayExceededException) {
                    throw new DiscordRateLimitException($exception->retryAfter, previous: $exception);
                }

                if ($exception instanceof RateLimitExceededException) {
                    throw new DiscordRateLimitException($exception->getRetryAfter(), $exception->isGlobal(), $exception);
                }

                if (! $exception instanceof ConnectException) {
                    throw $exception;
                }

                if ($attempt === $attempts - 1) {
                    throw new DiscordTransportException($exception->getMessage(), $exception->getCode(), previous: $exception);
                }

                Sleep::usleep((1 + $attempt * 2) * 1_000_000);
                $this->rewindMultipart($options[RequestOptions::MULTIPART] ?? []);

                continue;
            }

            if (! in_array($response->getStatusCode(), [500, 502, 504, 524], true) || $attempt === $attempts - 1) {
                break;
            }

            Sleep::usleep((1 + $attempt * 2) * 1_000_000);
            $this->rewindMultipart($options[RequestOptions::MULTIPART] ?? []);
        }

        if (! $response instanceof ResponseInterface) {
            throw new DiscordTransportException('Discord request completed without a response.');
        }

        $contents = (string) $response->getBody();
        $discordResponse = new DiscordResponse($response, $contents);
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return $discordResponse;
        }

        throw match ($response->getStatusCode()) {
            401 => new DiscordAuthenticationException($discordResponse, $discordRequest),
            403 => new DiscordForbiddenException($discordResponse, $discordRequest),
            404 => new DiscordNotFoundException($discordResponse, $discordRequest),
            500, 502, 504, 524 => new DiscordServerException($discordResponse, $discordRequest),
            default => new DiscordHttpException($discordResponse, $discordRequest),
        };
    }

    /** @param array<string, mixed> $query */
    private function queryString(array $query): string
    {
        $parts = [];
        foreach ($query as $key => $value) {
            foreach (is_array($value) ? $value : [$value] as $item) {
                $item = is_bool($item) ? ($item ? 'true' : 'false') : $item;
                if ($item !== null) {
                    $parts[] = rawurlencode((string) $key).'='.rawurlencode((string) $item);
                }
            }
        }

        return $parts === [] ? '' : '?'.implode('&', $parts);
    }

    /** @param list<array<string, mixed>> $multipart */
    private function rewindMultipart(array $multipart): void
    {
        foreach ($multipart as $part) {
            if (is_resource($part['contents'] ?? null)) {
                rewind($part['contents']);
            } elseif (($part['contents'] ?? null) instanceof StreamInterface && $part['contents']->isSeekable()) {
                $part['contents']->rewind();
            }
        }
    }
}
