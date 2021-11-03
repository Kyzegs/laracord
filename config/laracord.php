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

    'client_id' =>  env('DISCORD_CLIENT_ID'),
    'client_secret' => env('DISCORD_CLIENT_SECRET'),
    'redirect' => env('DISCORD_REDIRECT_URI', '/callback'),
    'scopes' => explode(',', env('DISCORD_SCOPES', 'identify,email,guilds')),

];
