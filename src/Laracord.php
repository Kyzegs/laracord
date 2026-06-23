<?php

declare(strict_types=1);

namespace Kyzegs\Laracord;

use Kyzegs\Laracord\ValueObjects\OAuthAccessToken;

class Laracord
{
    public static function bot(): DiscordClient
    {
        return resolve(LaracordManager::class)->bot();
    }

    public static function bearer(string|OAuthAccessToken $token): DiscordClient
    {
        return resolve(LaracordManager::class)->bearer($token);
    }
}
