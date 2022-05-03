<?php

namespace Kyzegs\Laracord\Services;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RateLimiter
{
    /**
     * @var int
     */
    const MAX_TTL = 60 * 60 * 24 * 7;

    /**
     * Method to match out major parameters of the route and
     * remove minor parameters / numbers. This has to be done
     * to ensure correct rate limiting according to:
     * https://discord.com/developers/docs/topics/rate-limits.
     *
     * @param  string  $url
     * @return string
     */
    private static function stripMinorParameters(string $url): string
    {
        $matches = [];

        if (
            (
                preg_match('/^(https:\/\/discord\.com\/api\/v\d+\/channels\/\d*).*?$/', $url, $matches) === 1 ||
                preg_match('/^(https:\/\/discord\.com\/api\/v\d+\/guilds\/\d*).*?$/', $url, $matches) === 1 ||
                preg_match('/^(https:\/\/discord\.com\/api\/v\d+\/users\/@me\/guilds\/\d*).*?$/', $url, $matches) === 1 ||
                preg_match('/^(https:\/\/discord\.com\/api\/v\d+\/webhooks\/\d*).*?$/', $url, $matches) === 1 ||
                preg_match('/^(https:\/\/discord\.com\/api\/v\d+\/applications\/\d*\/guilds\/\d*).*?$/', $url, $matches) === 1
            ) && count($matches) === 2
        ) {
            $url = $matches[1].preg_replace('/[0-9]+/', '', substr($url, strlen($matches[1])));
        }

        return $url;
    }

    /**
     * Returns the route for the current URL.
     *
     * @param  Request  $request
     * @return string
     */
    private static function getRoute(Request $request): string
    {
        return self::stripMinorParameters($request->url());
    }

    /**
     * Returns what is considered the time when a given request is being made.
     *
     * @return string|float
     */
    private static function getRequestTime(): string|float
    {
        return microtime(true);
    }

    /**
     * Returns when the last request was made.
     *
     * @param  Request  $request
     * @return float|null
     */
    private static function getLastRequestTime(Request $request): float|null
    {
        return Cache::get(sprintf('last_request_time:%s', self::getRoute($request)));
    }

    /**
     * Used to set the current time as the last request time to be queried when the next request is attempted.
     *
     * @param  Request  $request
     * @return void
     */
    private static function setLastRequestTime(Request $request): void
    {
        Cache::put(sprintf('last_request_time:%s', self::getRoute($request)), self::getRequestTime(), static::MAX_TTL);
    }

    /**
     * Returns the minimum amount of time that is required to have passed since the last request was made. This value is used to determine if the current request should be delayed, based on when the last request was made.
     *
     * @param  Request  $request
     * @return float
     */
    private static function getRequestAllowance(Request $request): float
    {
        $key = sprintf('request_allowance:%s', self::getRoute($request));

        return Cache::has($key)
            ? (Cache::get($key) - time()) * 1000000
            : 0;
    }

    /**
     * Used to set the minimum amount of time that is required to pass between this request and the next (in microseconds).
     *
     * @param  Request  $request
     * @param  Response  $response
     * @return void
     */
    public static function setRequestAllowance(Request $request, Response $response): void
    {
        $remaining = $response->header('x-ratelimit-remaining');
        $reset = $response->header('x-ratelimit-reset');
        $retryAfter = $response->header('x-ratelimit-reset-after');

        Log::info('X-RateLimit-Remaining: ' . $remaining);
        Log::info('X-Ratelimit-Bucket: ' . $response->header('x-ratelimit-bucket'));

        if ($remaining == 0 && $retryAfter) {
            $diff = $retryAfter;
            $epoch = substr_replace(Carbon::now()->addSeconds($retryAfter)->getPreciseTimestamp(3), '.', 10, 0);
        } else {
            $diff = Carbon::createFromTimestampUTC($reset)->diffInSeconds(Carbon::now());
            $epoch = $reset;
        }

        if ($remaining == 0 && $response->status() !== 429) {
            Log::debug(sprintf('A rate limit bucket has been exhausted. Continuing in %.2f second(s)', $diff));
            Cache::put(sprintf('request_allowance:%s', self::getRoute($request)), $epoch, static::MAX_TTL);
        }
        // else if ($response->status() === 429) {
        //     Log::debug(sprintf('We are being rate limited. Retrying in %.2f second(s)', $diff));
        //     self::delayFromHeader($response);
        //     Log::debug('Done sleeping. Retrying the previously rate-limited request');
        // }
    }

    /**
     * Returns the delay duration for the given request (in microseconds).
     *
     * @param  Request  $request
     * @return float
     */
    protected static function getDelay(Request $request): float
    {
        $lastRequestTime = self::getLastRequestTime($request);
        $requestAllowance = self::getRequestAllowance($request);
        $requestTime = self::getRequestTime();

        return max(0, $requestAllowance - ($requestTime - $lastRequestTime));
    }

    /**
     * @param Request $request
     * @return void
     */
    public static function delay(Request $request)
    {
        while (($delay = self::getDelay($request)) > 0) {
            usleep($delay);
        }

        self::setLastRequestTime($request);
    }

    /**
     * @param Response $response
     * @return void
     */
    public static function delayFromHeader(Response $response)
    {
        usleep($response->header('x-ratelimit-reset-after') * 1000000);
    }
}
