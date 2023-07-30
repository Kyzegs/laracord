<?php

namespace Kyzegs\Laracord;

use GuzzleHttp\Client as GuzzleClient;

class Factory
{
    public function make(): Client
    {
        $client = new GuzzleClient([
            'base_uri' => Route::BASE_URL,
            'headers' => [
                'Authorization' => sprintf('Bot %s', config('laracord.bot_token')),
            ],
        ]);

        return new Client($client);
    }
}
