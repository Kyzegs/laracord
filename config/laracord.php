<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Discord OAuth2
    |--------------------------------------------------------------------------
    |
    | Options used within the OAuth2 process for Discord.
    |
    */

    'oauth' => [
        'client_id' => env('DISCORD_CLIENT_ID'),
        'client_secret' => env('DISCORD_CLIENT_SECRET'),
        'redirect' => env('DISCORD_REDIRECT_URI', '/callback'),
        'scopes' => array_values(array_filter(explode(',', (string) env('DISCORD_SCOPES', 'identify,email,guilds')))),
    ],

    /*
    |--------------------------------------------------------------------------
    | Discord Bot
    |--------------------------------------------------------------------------
    |
    | Options that are needed to make requests for bot API routes.
    |
    */

    'bot_token' => env('DISCORD_BOT_TOKEN'),
    'public_key' => env('DISCORD_PUBLIC_KEY'),
    'application_id' => env('DISCORD_APPLICATION_ID'),
    'api_url' => env('DISCORD_API_URL', 'https://discord.com/api'),
    'api_version' => (int) env('DISCORD_API_VERSION', 10),
    'user_agent' => env('DISCORD_USER_AGENT', 'DiscordBot (https://github.com/Kyzegs/laracord, 1.0.0)'),

    /*
    |--------------------------------------------------------------------------
    | Application Commands
    |--------------------------------------------------------------------------
    |
    | Slash/application command definitions pushed to Discord by the
    | `laracord:commands:sync` Artisan command. Each entry is a command object
    | as documented by Discord (name, description, options, ...).
    |
    */

    'commands' => [],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configuration for the rate limiting middleware.
    |
    */

    'rate_limit' => [
        'cache_prefix' => env('LARACORD_CACHE_PREFIX', 'laracord:rate-limit:'),
        'lock_prefix' => env('LARACORD_LOCK_PREFIX', 'laracord:lock:'),
        'max_retries' => (int) env('LARACORD_RATE_LIMIT_RETRIES', 5),
        'safety_buffer_seconds' => (float) env('LARACORD_RATE_LIMIT_BUFFER', 0.25),
        'jitter_percent' => (float) env('LARACORD_RATE_LIMIT_JITTER', 2.0),
        'max_delay_seconds' => env('LARACORD_MAX_RATE_LIMIT_DELAY'),
        'global_requests' => (int) env('LARACORD_GLOBAL_REQUESTS', 50),
        'global_window_seconds' => 1.0,
        'invalid_requests' => (int) env('LARACORD_INVALID_REQUESTS', 9000),
        'invalid_window_seconds' => 600.0,
        'lock_ttl_seconds' => (int) env('LARACORD_LOCK_TTL', 60),
        'lock_wait_seconds' => (int) env('LARACORD_LOCK_WAIT', 10),
    ],

    'http' => [
        'timeout' => (float) env('LARACORD_HTTP_TIMEOUT', 30),
        'connect_timeout' => (float) env('LARACORD_CONNECT_TIMEOUT', 10),
        'server_retries' => (int) env('LARACORD_SERVER_RETRIES', 5),
    ],

    'signatures' => [
        'max_age_seconds' => (int) env('LARACORD_SIGNATURE_MAX_AGE', 300),
    ],

    'observability' => [
        'telescope' => (bool) env('LARACORD_TELESCOPE', true),
    ],

];
