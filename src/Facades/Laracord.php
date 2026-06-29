<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Facades;

use Illuminate\Support\Facades\Facade;
use Kyzegs\Laracord\Http\DiscordResponse;
use Kyzegs\Laracord\LaracordManager;
use Kyzegs\Laracord\Testing\FakeLaracord;

/**
 * @method static \Kyzegs\Laracord\DiscordClient bot()
 * @method static \Kyzegs\Laracord\DiscordClient bearer(\Kyzegs\Laracord\ValueObjects\OAuthAccessToken|string $token)
 * @method static \Kyzegs\Laracord\DiscordClient withoutAuthentication()
 * @method static \Kyzegs\Laracord\Interactions\InteractionRouter interactions()
 *
 * @see LaracordManager
 */
final class Laracord extends Facade
{
    /**
     * Swap the Discord client for a recording fake and return it.
     *
     * @param  array<string, mixed>  $stubs
     */
    public static function fake(array $stubs = []): FakeLaracord
    {
        $fakeLaracord = new FakeLaracord($stubs);
        self::swap($fakeLaracord);

        return $fakeLaracord;
    }

    /**
     * Build a canned Discord response for use as a fake stub value.
     *
     * @param  array<string, mixed>|string  $body
     * @param  array<string, string>  $headers
     */
    public static function response(array|string $body = '', int $status = 200, array $headers = []): DiscordResponse
    {
        return FakeLaracord::response($body, $status, $headers);
    }

    protected static function getFacadeAccessor(): string
    {
        return 'laracord';
    }
}
