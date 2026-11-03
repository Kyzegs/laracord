<?php

declare(strict_types=1);

use Kyzegs\Laracord\Interactions\InteractionResponse;
use Kyzegs\Laracord\Payloads\DiscordEmbed;
use Kyzegs\Laracord\Payloads\DiscordMessage;
use Kyzegs\Laracord\Resources\ResourceClient;

it('defaults to no parsed mentions and serializes embeds', function (): void {
    $discordMessage = (new DiscordMessage)
        ->content('Hello')
        ->embed((new DiscordEmbed)->title('Notice')->description('Safe content'));

    expect($discordMessage->toArray())
        ->toMatchArray(['content' => 'Hello', 'allowed_mentions' => ['parse' => []]])
        ->and($discordMessage->toArray()['embeds'][0]['title'])->toBe('Notice');
});

it('uploads files attached to a message body', function (): void {
    $request = ResourceClient::buildRequest('messages', 'create', ['channel_id' => '1'], (new DiscordMessage)->file('data', 'a.txt'));

    expect($request->files)->toHaveCount(1)
        ->and($request->files[0]['filename'])->toBe('a.txt');
});

it('prefers files passed explicitly', function (): void {
    $files = [['contents' => 'other', 'filename' => 'b.txt']];

    $request = ResourceClient::buildRequest('messages', 'create', ['channel_id' => '1'], (new DiscordMessage)->file('data', 'a.txt'), files: $files);

    expect($request->files)->toBe($files);
});

it('refuses to silently drop files from a direct interaction response', function (string $response): void {
    expect(fn () => InteractionResponse::$response((new DiscordMessage)->file('data', 'a.txt')))
        ->toThrow(InvalidArgumentException::class, 'files');
})->with(['message', 'update']);

it('validates stable discord message limits', function (): void {
    expect(fn (): DiscordMessage => (new DiscordMessage)->content(str_repeat('x', 2001)))
        ->toThrow(InvalidArgumentException::class);
});
