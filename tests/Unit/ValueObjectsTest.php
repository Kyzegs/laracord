<?php

declare(strict_types=1);

use Kyzegs\Laracord\ValueObjects\Authentication;
use Kyzegs\Laracord\ValueObjects\DiscordWebhook;
use Kyzegs\Laracord\ValueObjects\Snowflake;

it('uses string snowflakes', function (): void {
    expect((string) new Snowflake('123456789012345678'))->toBe('123456789012345678');
});

it('redacts webhook tokens', function (): void {
    $discordWebhook = DiscordWebhook::fromUrl('https://discord.com/api/webhooks/123456789012345678/secret-token');

    expect((string) $discordWebhook)->not->toContain('secret-token')
        ->and($discordWebhook->token())->toBe('secret-token');
});

it('isolates authentication fingerprints', function (): void {
    expect(Authentication::bot('one')->fingerprint())
        ->not->toBe(Authentication::bearer('one')->fingerprint());
});
