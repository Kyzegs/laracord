<?php

namespace Kyzegs\Laracord\Client;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \GuzzleHttp\Promise\PromiseInterface response($body = null, $status = 200, $headers = [])
 * @method static \Kyzegs\Laracord\Client\Factory fake($callback = null)
 * @method static \Kyzegs\Laracord\Client\PendingRequest accept(string $contentType)
 * @method static \Kyzegs\Laracord\Client\PendingRequest acceptJson()
 * @method static \Kyzegs\Laracord\Client\PendingRequest asForm()
 * @method static \Kyzegs\Laracord\Client\PendingRequest asJson()
 * @method static \Kyzegs\Laracord\Client\PendingRequest asMultipart()
 * @method static \Kyzegs\Laracord\Client\PendingRequest async()
 * @method static \Kyzegs\Laracord\Client\PendingRequest attach(string|array $name, string $contents = '', string|null $filename = null, array $headers = [])
 * @method static \Kyzegs\Laracord\Client\PendingRequest baseUrl(string $url)
 * @method static \Kyzegs\Laracord\Client\PendingRequest beforeSending(callable $callback)
 * @method static \Kyzegs\Laracord\Client\PendingRequest bodyFormat(string $format)
 * @method static \Kyzegs\Laracord\Client\PendingRequest contentType(string $contentType)
 * @method static \Kyzegs\Laracord\Client\PendingRequest dd()
 * @method static \Kyzegs\Laracord\Client\PendingRequest dump()
 * @method static \Kyzegs\Laracord\Client\PendingRequest retry(int $times, int $sleep = 0, ?callable $when = null)
 * @method static \Kyzegs\Laracord\Client\PendingRequest sink(string|resource $to)
 * @method static \Kyzegs\Laracord\Client\PendingRequest stub(callable $callback)
 * @method static \Kyzegs\Laracord\Client\PendingRequest timeout(int $seconds)
 * @method static \Kyzegs\Laracord\Client\PendingRequest withBasicAuth(string $username, string $password)
 * @method static \Kyzegs\Laracord\Client\PendingRequest withBody(resource|string $content, string $contentType)
 * @method static \Kyzegs\Laracord\Client\PendingRequest withCookies(array $cookies, string $domain)
 * @method static \Kyzegs\Laracord\Client\PendingRequest withDigestAuth(string $username, string $password)
 * @method static \Kyzegs\Laracord\Client\PendingRequest withHeaders(array $headers)
 * @method static \Kyzegs\Laracord\Client\PendingRequest withMiddleware(callable $middleware)
 * @method static \Kyzegs\Laracord\Client\PendingRequest withOptions(array $options)
 * @method static \Kyzegs\Laracord\Client\PendingRequest withToken(string $token, string $type = 'Bearer')
 * @method static \Kyzegs\Laracord\Client\PendingRequest withUserAgent(string $userAgent)
 * @method static \Kyzegs\Laracord\Client\PendingRequest withoutRedirecting()
 * @method static \Kyzegs\Laracord\Client\PendingRequest withoutVerifying()
 * @method static array pool(callable $callback)
 * @method static \Illuminate\Http\Client\Response delete(string $url, array $data = [])
 * @method static \Illuminate\Http\Client\Response get(string $url, array|string|null $query = null)
 * @method static \Illuminate\Http\Client\Response head(string $url, array|string|null $query = null)
 * @method static \Illuminate\Http\Client\Response patch(string $url, array $data = [])
 * @method static \Illuminate\Http\Client\Response post(string $url, array $data = [])
 * @method static \Illuminate\Http\Client\Response put(string $url, array $data = [])
 * @method static \Illuminate\Http\Client\Response send(string $method, string $url, array $options = [])
 * @method static \Illuminate\Http\Client\ResponseSequence fakeSequence(string $urlPattern = '*')
 * @method static void assertSent(callable $callback)
 * @method static void assertSentInOrder(array $callbacks)
 * @method static void assertNotSent(callable $callback)
 * @method static void assertNothingSent()
 * @method static void assertSentCount(int $count)
 * @method static void assertSequencesAreEmpty()
 *
 * @see \Kyzegs\Laracord\Client\Factory
 */
class Http extends Facade
{
    /**
     * Get the registered name of the component.
     *
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor(): string
    {
        return Factory::class;
    }
}
