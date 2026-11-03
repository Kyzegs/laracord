<?php

declare(strict_types=1);

use Kyzegs\Laracord\Components\TextDisplay;
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

it('omits attachment metadata when no file has any', function (): void {
    expect((new DiscordMessage)->file('data', 'a.txt')->toArray())->not->toHaveKey('attachments');
});

it('describes every upload once one has metadata, matching ids to file positions', function (): void {
    $message = (new DiscordMessage)
        ->file('one', 'a.txt')
        ->file('two', 'b.png', 'image/png', 'A chart', spoiler: true);

    expect($message->toArray()['attachments'])->toBe([
        ['id' => 0, 'filename' => 'a.txt'],
        ['id' => 1, 'filename' => 'b.png', 'description' => 'A chart', 'is_spoiler' => true],
    ]);
});

it('keeps an empty file instead of dropping its contents', function (): void {
    expect((new DiscordMessage)->file('', 'empty.txt')->files()[0]['contents'])->toBe('');
});

it('builds replies and forwards', function (): void {
    expect((new DiscordMessage)->reply('5')->toArray()['message_reference'])
        ->toBe(['type' => 0, 'message_id' => '5', 'fail_if_not_exists' => true])
        ->and((new DiscordMessage)->reply('5', failIfNotExists: false)->toArray()['message_reference']['fail_if_not_exists'])->toBeFalse()
        ->and((new DiscordMessage)->forward('5', '9')->toArray()['message_reference'])
        ->toBe(['type' => 1, 'message_id' => '5', 'channel_id' => '9']);
});

it('sets the remaining message fields', function (): void {
    $message = (new DiscordMessage)
        ->stickers(['1', '2'])
        ->nonce('abc')
        ->threadName('Topic')
        ->appliedTags(['7'])
        ->sharedClientTheme(['colors' => ['5865F2'], 'gradient_angle' => 0, 'base_mix' => 50]);

    expect($message->toArray())->toMatchArray([
        'sticker_ids' => ['1', '2'],
        'nonce' => 'abc',
        'enforce_nonce' => true,
        'thread_name' => 'Topic',
        'applied_tags' => ['7'],
        'shared_client_theme' => ['colors' => ['5865F2'], 'gradient_angle' => 0, 'base_mix' => 50],
    ]);
});

it('rejects more than three stickers', function (): void {
    expect(fn (): DiscordMessage => (new DiscordMessage)->stickers(['1', '2', '3', '4']))->toThrow(InvalidArgumentException::class);
});

it('rejects fields Discord forbids in a components v2 message', function (): void {
    $withStickers = (new DiscordMessage)->componentsV2([TextDisplay::make('Hi')])->stickers(['1']);
    $withTheme = (new DiscordMessage)->componentsV2([TextDisplay::make('Hi')])->sharedClientTheme(['colors' => ['5865F2'], 'gradient_angle' => 0, 'base_mix' => 50]);

    expect(fn (): array => $withStickers->toArray())->toThrow(InvalidArgumentException::class, 'sticker_ids')
        ->and(fn (): array => $withTheme->toArray())->toThrow(InvalidArgumentException::class, 'shared_client_theme');
});

it('validates stable discord message limits', function (): void {
    expect(fn (): DiscordMessage => (new DiscordMessage)->content(str_repeat('x', 2001)))
        ->toThrow(InvalidArgumentException::class);
});
