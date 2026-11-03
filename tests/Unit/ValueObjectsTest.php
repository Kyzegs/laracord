<?php

declare(strict_types=1);

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

it('accepts webhook URLs copied from any Discord client', function (string $host): void {
    expect(DiscordWebhook::fromUrl('https://'.$host.'/api/webhooks/123456789012345678/token')->token())->toBe('token');
})->with(['discord.com', 'discordapp.com', 'ptb.discord.com', 'canary.discord.com']);

it('rejects webhook URLs on other hosts', function (string $url): void {
    expect(fn (): DiscordWebhook => DiscordWebhook::fromUrl($url))->toThrow(InvalidArgumentException::class);
})->with([
    'lookalike' => ['https://discord.com.evil.test/api/webhooks/123456789012345678/token'],
    'unknown subdomain' => ['https://evil.discord.com/api/webhooks/123456789012345678/token'],
    'http' => ['http://discord.com/api/webhooks/123456789012345678/token'],
]);

it('rejects webhook URLs for other payload formats instead of dropping the suffix', function (string $suffix): void {
    expect(fn (): DiscordWebhook => DiscordWebhook::fromUrl('https://discord.com/api/webhooks/123456789012345678/token/'.$suffix))
        ->toThrow(InvalidArgumentException::class);
})->with(['slack', 'github']);
