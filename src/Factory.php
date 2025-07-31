<?php

declare(strict_types=1);

namespace Kyzegs\Laracord;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use Kyzegs\Laracord\Middleware\RatelimitMiddleware;

class Factory
{
    public function make(): Client
    {
        $handlerStack = HandlerStack::create();
        $handlerStack->push(new RatelimitMiddleware);

        $client = new GuzzleClient([
            'handler' => $handlerStack,
            'base_uri' => Route::BASE_URL,
            'headers' => [
                'Authorization' => sprintf('Bot %s', config('laracord.bot_token')),
            ],
        ]);

        return new Client($client);
    }
}
