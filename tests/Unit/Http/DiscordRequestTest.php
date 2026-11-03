<?php

declare(strict_types=1);

use Kyzegs\Laracord\Enums\HttpMethod;
use Kyzegs\Laracord\Http\DiscordRequest;
use Kyzegs\Laracord\ValueObjects\Snowflake;

it('resolves and encodes route parameters', function (): void {
    $request = new DiscordRequest(HttpMethod::GET, '/channels/{channel_id}/reactions/{emoji}', [
        'channel_id' => new Snowflake('123'),
        'emoji' => 'party:456',
    ]);

    expect($request->resolvedPath())->toBe('/channels/123/reactions/party%3A456');
});

it('rejects unresolved route parameters', function (): void {
    expect(fn (): string => (new DiscordRequest(HttpMethod::GET, '/channels/{channel_id}'))->resolvedPath())
        ->toThrow(InvalidArgumentException::class);
});

it('keeps tokens out of the missing parameter error', function (): void {
    $request = new DiscordRequest(HttpMethod::GET, '/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}', [
        'webhook_id' => '1',
        'webhook_token' => 'secret-token',
    ]);

    expect(fn (): string => $request->resolvedPath())
        ->toThrow(InvalidArgumentException::class, '/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}');
});
