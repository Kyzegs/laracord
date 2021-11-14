<?php

namespace Kyzegs\Laracord\Client;

/**
 * @method \Kyzegs\Laracord\Client\PendingRequest accept(string $contentType)
 * @method \Kyzegs\Laracord\Client\PendingRequest acceptJson()
 * @method \Kyzegs\Laracord\Client\PendingRequest asForm()
 * @method \Kyzegs\Laracord\Client\PendingRequest asJson()
 * @method \Kyzegs\Laracord\Client\PendingRequest asMultipart()
 * @method \Kyzegs\Laracord\Client\PendingRequest async()
 * @method \Kyzegs\Laracord\Client\PendingRequest attach(string|array $name, string $contents = '', string|null $filename = null, array $headers = [])
 * @method \Kyzegs\Laracord\Client\PendingRequest baseUrl(string $url)
 * @method \Kyzegs\Laracord\Client\PendingRequest beforeSending(callable $callback)
 * @method \Kyzegs\Laracord\Client\PendingRequest bodyFormat(string $format)
 * @method \Kyzegs\Laracord\Client\PendingRequest contentType(string $contentType)
 * @method \Kyzegs\Laracord\Client\PendingRequest dd()
 * @method \Kyzegs\Laracord\Client\PendingRequest dump()
 * @method \Kyzegs\Laracord\Client\PendingRequest retry(int $times, int $sleep = 0, ?callable $when = null)
 * @method \Kyzegs\Laracord\Client\PendingRequest sink(string|resource $to)
 * @method \Kyzegs\Laracord\Client\PendingRequest stub(callable $callback)
 * @method \Kyzegs\Laracord\Client\PendingRequest timeout(int $seconds)
 * @method \Kyzegs\Laracord\Client\PendingRequest withBasicAuth(string $username, string $password)
 * @method \Kyzegs\Laracord\Client\PendingRequest withBody(resource|string $content, string $contentType)
 * @method \Kyzegs\Laracord\Client\PendingRequest withCookies(array $cookies, string $domain)
 * @method \Kyzegs\Laracord\Client\PendingRequest withDigestAuth(string $username, string $password)
 * @method \Kyzegs\Laracord\Client\PendingRequest withHeaders(array $headers)
 * @method \Kyzegs\Laracord\Client\PendingRequest withMiddleware(callable $middleware)
 * @method \Kyzegs\Laracord\Client\PendingRequest withOptions(array $options)
 * @method \Kyzegs\Laracord\Client\PendingRequest withToken(string $token, string $type = 'Bearer')
 * @method \Kyzegs\Laracord\Client\PendingRequest withUserAgent(string $userAgent)
 * @method \Kyzegs\Laracord\Client\PendingRequest withoutRedirecting()
 * @method \Kyzegs\Laracord\Client\PendingRequest withoutVerifying()
 * @method array pool(callable $callback)
 * @method \Illuminate\Http\Client\Response delete(string $url, array $data = [])
 * @method \Illuminate\Http\Client\Response get(string $url, array|string|null $query = null)
 * @method \Illuminate\Http\Client\Response head(string $url, array|string|null $query = null)
 * @method \Illuminate\Http\Client\Response patch(string $url, array $data = [])
 * @method \Illuminate\Http\Client\Response post(string $url, array $data = [])
 * @method \Illuminate\Http\Client\Response put(string $url, array $data = [])
 * @method \Illuminate\Http\Client\Response send(string $method, string $url, array $options = [])
 *
 * @see \Kyzegs\Laracord\Client\PendingRequest
 */
class Factory extends \Illuminate\Http\Client\Factory
{
    /**
     * Create a new pending request instance for this factory.
     *
     * @return \Kyzegs\Laracord\Client\PendingRequest
     */
    protected function newPendingRequest(): PendingRequest
    {
        return new PendingRequest($this);
    }
}
