<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Facades;

use Illuminate\Support\Facades\Facade;
use Kyzegs\Laracord\LaracordManager;

/**
 * @method static \Kyzegs\Laracord\DiscordClient bot()
 * @method static \Kyzegs\Laracord\DiscordClient bearer(string|\Kyzegs\Laracord\ValueObjects\OAuthAccessToken $token)
 * @method static \Kyzegs\Laracord\DiscordClient withoutAuthentication()
 *
 * @see LaracordManager
 */
final class Laracord extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laracord';
    }
}
