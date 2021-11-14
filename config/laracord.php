<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Discord OAuth2
    |--------------------------------------------------------------------------
    |
    | Options used within the OAuth2 process for Discord.
    |
    */

    'client_id' => env('DISCORD_CLIENT_ID'),
    'client_secret' => env('DISCORD_CLIENT_SECRET'),
    'redirect' => env('DISCORD_REDIRECT_URI', '/callback'),
    'scopes' => explode(',', env('DISCORD_SCOPES', 'identify,email,guilds')),

    /*
    |--------------------------------------------------------------------------
    | Discord Bot
    |--------------------------------------------------------------------------
    |
    | Options that are needed to make requests for bot API routes.
    |
    */

    'bot_token' => env('DISCORD_BOT_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Session
    |--------------------------------------------------------------------------
    |
    | Here you may specify what should be stored in the Laravel session.
    |
    | Disabling the storing of the user will make it so you cannot use any
    | user API routes without adding the bearer token manually.
    |
    */

    'session' => [
        'user' => [
            'store' => true,
            'key' => 'user',
        ],
    ],

];
