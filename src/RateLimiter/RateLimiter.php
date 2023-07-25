<?php

namespace Kyzegs\Laracord\RateLimiter;

use Closure;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RateLimiter
{
    /** @var \Kyzegs\Laracord\RateLimiter\RateLimitProvider */
    protected $provider;

    /**
     * Creates a callable middleware rate limiter.
     *
     * @return void
     */
    public function __construct()
    {
        $this->provider = new RateLimitProvider();
    }

    /**
     * Delay the request then sets the allowance for the next request.
     */
    public function __invoke(callable $handler): Closure
    {
        return function (RequestInterface $request, $options) use ($handler) {
            while (($delay = $this->getDelay($request)) > 0) {
                $this->delay($delay);
            }

            $this->provider->setLastRequestTime($request);

            return $handler($request, $options)->then($this->setAllowance($request));
        };
    }

    /**
     * Returns the delay duration for the given request (in microseconds).
     */
    protected function getDelay(RequestInterface $request): float
    {
        $lastRequestTime = $this->provider->getLastRequestTime($request);
        $requestAllowance = $this->provider->getRequestAllowance($request);
        $requestTime = $this->provider->getRequestTime($request);

        return max(0, $requestAllowance - ($requestTime - $lastRequestTime));
    }

    /**
     * Delays the given request by an amount of microseconds.
     */
    protected function delay(float $time): void
    {
        usleep($time);
    }

    /**
     * Returns a callable handler which allows the provider to set the request
     * allowance for the next request, using the current response.
     */
    protected function setAllowance(RequestInterface $request): Closure
    {
        return function (ResponseInterface $response) use ($request) {
            $this->provider->setRequestAllowance($request, $response);

            return $response;
        };
    }
}
