<?php

namespace Kyzegs\Laracord\RateLimiter;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractRateLimitProvider
{
    /**
     * Returns the route for the current URL.
     *
     * @param  \Psr\Http\Message\RequestInterface  $request
     * @return string
     */
    public function getRoute(RequestInterface $request): string
    {
        return $this->stripMinorParameters((string) $request->getUri());
    }

    /**
     * Returns what is considered the time when a given request is being made.
     *
     * @return string|float
     */
    final public function getRequestTime(): string|float
    {
        return microtime(true);
    }

    /**
     * Returns when the last request was made.
     *
     * @param  RequestInterface  $request
     * @return float|null
     */
    abstract public function getLastRequestTime(RequestInterface $request): float|null;

    /**
     * Used to set the current time as the last request time to be queried when
     * the next request is attempted.
     *
     * @param  RequestInterface  $request
     * @return void
     */
    abstract public function setLastRequestTime(RequestInterface $request): void;

    /**
     * Returns the minimum amount of time that is required to have passed since
     * the last request was made. This value is used to determine if the current
     * request should be delayed, based on when the last request was made.
     *
     * Returns the allowed  between the last request and the next, which
     * is used to determine if a request should be delayed and by how much.
     *
     * @param  RequestInterface  $request
     * @return float
     */
    abstract public function getRequestAllowance(RequestInterface $request): float;

    /**
     * Used to set the minimum amount of time that is required to pass between
     * this request and the next (in microseconds).
     *
     * @param  RequestInterface  $request
     * @param  ResponseInterface  $response
     * @return void
     */
    abstract public function setRequestAllowance(RequestInterface $request, ResponseInterface $response): void;

    /**
     * Method to match out major parameters of the route and
     * remove minor parameters / numbers. This has to be done
     * to ensure correct rate limiting according to:
     * https://discord.com/developers/docs/topics/rate-limits.
     *
     * @param  string  $url
     * @return string
     */
    protected function stripMinorParameters(string $url): string
    {
        $matches = [];

        if (
            (
                preg_match('/^(https:\/\/discord\.com\/api\/v\d+\/channels\/\d*).*?$/', $url, $matches) === 1 ||
                preg_match('/^(https:\/\/discord\.com\/api\/v\d+\/guilds\/\d*).*?$/', $url, $matches) === 1 ||
                preg_match('/^(https:\/\/discord\.com\/api\/v\d+\/users\/@me\/guilds\/\d*).*?$/', $url, $matches) === 1 ||
                preg_match('/^(https:\/\/discord\.com\/api\/v\d+\/webhooks\/\d*).*?$/', $url, $matches) === 1
            ) && count($matches) === 2
        ) {
            $url = $matches[1].preg_replace('/[0-9]+/', '', substr($url, strlen($matches[1])));
        }

        return $url;
    }
}
