<?php

declare(strict_types=1);

use Kyzegs\Laracord\Enums\HttpMethod;
use Kyzegs\Laracord\Http\DiscordRequest;
use Kyzegs\Laracord\ValueObjects\Snowflake;

it('resolves and encodes route parameters', function (): void {
    $request = new DiscordRequest(HttpMethod::Get, '/channels/{channel_id}/reactions/{emoji}', [
        'channel_id' => new Snowflake('123'),
        'emoji' => 'party:456',
    ]);

    expect($request->resolvedPath())->toBe('/channels/123/reactions/party%3A456');
});

it('rejects unresolved route parameters', function (): void {
    expect(fn (): string => (new DiscordRequest(HttpMethod::Get, '/channels/{channel_id}'))->resolvedPath())
        ->toThrow(InvalidArgumentException::class);
});
