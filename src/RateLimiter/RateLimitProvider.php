<?php

namespace Kyzegs\Laracord\RateLimiter;

use Illuminate\Support\Facades\Cache;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RateLimitProvider extends AbstractRateLimitProvider
{
    /** @var int */
    const MAX_TTL = 60 * 60 * 24 * 7;

    /**
     * Returns when the last request was made.
     *
     * @param  RequestInterface  $request
     * @return float|null
     */
    public function getLastRequestTime(RequestInterface $request): float|null
    {
        $route = $this->getRoute($request);

        return Cache::get($route.'last_request_time');
    }

    /**
     * Used to set the current time as the last request time to be queried when the next request is attempted.
     *
     * @param  RequestInterface  $request
     * @return void
     */
    public function setLastRequestTime(RequestInterface $request): void
    {
        $route = $this->getRoute($request);

        Cache::put($route.'last_request_time', $this->getRequestTime($request), static::MAX_TTL);
    }

    /**
     * Returns the minimum amount of time that is required to have passed since the last request was made. This value is used to determine if the current request should be delayed, based on when the last request was made.
     *
     * @param  RequestInterface  $request
     * @return float
     */
    public function getRequestAllowance(RequestInterface $request): float
    {
        $route = $this->getRoute($request);
        $key = $route.'request_allowance';

        if (! Cache::has($key)) {
            return 0;
        }

        return (Cache::get($key) - time()) * 1000000;
    }

    /**
     * Used to set the minimum amount of time that is required to pass between this request and the next (in microseconds).
     *
     * @param  RequestInterface  $request
     * @param  ResponseInterface  $response
     * @return void
     */
    public function setRequestAllowance(RequestInterface $request, ResponseInterface $response): void
    {
        $route = $this->getRoute($request);

        $remaining = $response->getHeader('x-ratelimit-remaining');
        $reset = $response->getHeader('x-ratelimit-reset');

        if (empty($remaining) || empty($reset) || (int) $remaining[0] > 0) {
            return;
        }

        // Extra 4 seconds as a safeguard as it's inconsistent without
        Cache::put($route.'request_allowance', $reset[0] + 4, static::MAX_TTL);
    }
}
