<?php

namespace Kyzegs\Laracord\Client;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Kyzegs\Laracord\Services\RateLimiter;

class PendingRequest extends \Illuminate\Http\Client\PendingRequest
{
    /**
     * The base URL for the request.
     *
     * @var string
     */
    protected $baseUrl = 'https://discord.com/api/v8';

    /**
     * Get a header for the pending request.
     *
     * @param  string  $headers
     * @return mixed
     */
    public function header(string $header): mixed
    {
        return Arr::get($this->headers(), $header);
    }

    /**
     * Get the headers for the pending request.
     *
     * @return array<string, mixed>
     */
    public function headers(): array
    {
        return Arr::get($this->options, 'headers', []);
    }

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
        if (! $this->header('Authorization')) {
            if (auth()->check() && Str::contains($url, 'users/@me')) {
                $this->withToken(auth()->user()->access_token);
            } else {
                $this->withToken(config('laracord.bot_token'), 'Bot');
            }
        }

        $this->retry(3, 0, function ($exception) {
            if ($exception->response->status() === 429) {
                Log::debug(sprintf('We are being rate limited. Retrying in %.2f second(s)', $exception->response->header('x-ratelimit-reset-after')));
                RateLimiter::delayFromHeader($exception->response);
                Log::debug('Done sleeping. Retrying the previously rate-limited request');
            }

            return $exception->response->status() === 429;
        })->beforeSending(function (Request $request) {
            RateLimiter::delay($request);
        });

        return parent::send($method, $url, $options);
    }
}
