<?php

declare(strict_types=1);

use Kyzegs\Laracord\Payloads\DiscordEmbed;
use Kyzegs\Laracord\Payloads\DiscordMessage;

it('defaults to no parsed mentions and serializes embeds', function (): void {
    $discordMessage = (new DiscordMessage)
        ->content('Hello')
        ->embed((new DiscordEmbed)->title('Notice')->description('Safe content'));

    expect($discordMessage->toArray())
        ->toMatchArray(['content' => 'Hello', 'allowed_mentions' => ['parse' => []]])
        ->and($discordMessage->toArray()['embeds'][0]['title'])->toBe('Notice');
});

it('validates stable discord message limits', function (): void {
    expect(fn (): DiscordMessage => (new DiscordMessage)->content(str_repeat('x', 2001)))
        ->toThrow(InvalidArgumentException::class);
});
