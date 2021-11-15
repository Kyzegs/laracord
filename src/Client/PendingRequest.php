<?php

namespace Kyzegs\Laracord\Client;

use GuzzleHttp\Client;
use Illuminate\Http\Client\Response;
use Kyzegs\Laracord\Constants\Routes;
use Kyzegs\Laracord\RateLimiter\RateLimiter;

class PendingRequest extends \Illuminate\Http\Client\PendingRequest
{
    /**
     * Send the request to the given URL.
     *
     * @param  string  $method
     * @param  string  $url
     * @param  array  $options
     * @return \Illuminate\Http\Client\Response
     *
     * @throws \Exception
     */
    public function send(string $method, string $url, array $options = []): Response
    {
       if (! array_key_exists('Authorization', $this->options['headers'] ?? [])) {
            if (str_starts_with($url, '/users/@me')) {
                $this->withToken(session(config('laracord.session.user.key'))?->token);
            } else {
                $this->withToken(config('laracord.bot_token'), 'Bot');
            }
        }

        return parent::send($method, $url, $options);
    }

    /**
     * Create new Guzzle client.
     *
     * @param  \GuzzleHttp\HandlerStack  $handlerStack
     * @return \GuzzleHttp\Client
     */
    public function createClient($handlerStack): Client
    {
        $handlerStack->push(new RateLimiter());

        return new Client([
            'handler' => $handlerStack,
            'base_uri' => Routes::BASE_URL,
            'cookies' => true,
        ]);
    }
}
