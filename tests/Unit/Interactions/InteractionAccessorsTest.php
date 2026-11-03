<?php

declare(strict_types=1);

use Kyzegs\Laracord\Components\FileUpload;
use Kyzegs\Laracord\Constants\Permissions;
use Kyzegs\Laracord\Interactions\Interaction;
use Kyzegs\Laracord\Interactions\InteractionResponse;

it('finds the invoking user in guilds and in DMs', function (): void {
    expect((new Interaction(['member' => ['user' => ['id' => '1']]]))->user())->toBe(['id' => '1'])
        ->and((new Interaction(['user' => ['id' => '2']]))->user())->toBe(['id' => '2'])
        ->and((new Interaction([]))->user())->toBeNull();
});

it('exposes the interaction context fields', function (): void {
    $interaction = new Interaction([
        'guild_id' => '1',
        'channel_id' => '2',
        'locale' => 'nl',
        'app_permissions' => (string) Permissions::PIN_MESSAGES,
        'entitlements' => [['id' => '3']],
        'attachment_size_limit' => 20971520,
        'context' => 0,
    ]);

    expect($interaction->guildId())->toBe('1')
        ->and($interaction->channelId())->toBe('2')
        ->and($interaction->locale())->toBe('nl')
        ->and($interaction->appPermissions() & Permissions::PIN_MESSAGES)->toBe(Permissions::PIN_MESSAGES)
        ->and($interaction->entitlements())->toBe([['id' => '3']])
        ->and($interaction->attachmentSizeLimit())->toBe(20971520)
        ->and($interaction->contextType())->toBe(0);
});

it('has safe defaults when context fields are absent', function (): void {
    $interaction = new Interaction([]);

    expect($interaction->guildId())->toBeNull()
        ->and($interaction->appPermissions())->toBe(0)
        ->and($interaction->entitlements())->toBe([])
        ->and($interaction->attachmentSizeLimit())->toBeNull()
        ->and($interaction->contextType())->toBeNull();
});

it('reads modal values however Discord nests them', function (string $customId, mixed $expected): void {
    $interaction = new Interaction(['type' => 5, 'data' => ['custom_id' => 'form', 'components' => [
        ['type' => 18, 'id' => 1, 'component' => ['type' => 4, 'custom_id' => 'name', 'value' => 'Ada']],
        ['type' => 18, 'id' => 2, 'component' => ['type' => 3, 'custom_id' => 'colour', 'values' => ['red', 'blue']]],
        ['type' => 18, 'id' => 3, 'component' => ['type' => 21, 'custom_id' => 'size', 'value' => 'large']],
        ['type' => 18, 'id' => 4, 'component' => ['type' => 22, 'custom_id' => 'extras', 'values' => ['a']]],
        ['type' => 18, 'id' => 5, 'component' => ['type' => 23, 'custom_id' => 'agree', 'value' => false]],
        ['type' => 18, 'id' => 6, 'component' => ['type' => 19, 'custom_id' => 'upload', 'values' => ['99']]],
        ['type' => 1, 'id' => 7, 'components' => [['type' => 4, 'custom_id' => 'legacy', 'value' => 'old']]],
    ]]]);

    expect($interaction->submitted($customId))->toBe($expected);
})->with([
    'text input' => ['name', 'Ada'],
    'select' => ['colour', ['red', 'blue']],
    'radio group' => ['size', 'large'],
    'checkbox group' => ['extras', ['a']],
    'unchecked checkbox' => ['agree', false],
    'file upload' => ['upload', ['99']],
    'legacy action row' => ['legacy', 'old'],
]);

it('falls back to the default for a modal value that was not submitted', function (): void {
    $interaction = new Interaction(['data' => ['components' => [
        ['type' => 18, 'component' => ['type' => 21, 'custom_id' => 'size', 'value' => null]],
    ]]]);

    expect($interaction->submitted('size', 'small'))->toBe('small')
        ->and($interaction->submitted('missing'))->toBeNull()
        ->and((new Interaction([]))->submitted('missing', 'x'))->toBe('x');
});

it('launches an activity', function (): void {
    expect(json_decode((string) InteractionResponse::launchActivity()->getContent(), true))->toBe(['type' => 12]);
});

it('filters file uploads by type', function (): void {
    expect(FileUpload::make('a')->fileTypes(['image', '.pdf'])->toArray()['file_types'])->toBe(['image', '.pdf'])
        ->and(FileUpload::make('a')->toArray())->not->toHaveKey('file_types');
});

it('rejects invalid file type filters', function (): void {
    expect(fn (): FileUpload => FileUpload::make('a')->fileTypes(['pdf']))->toThrow(InvalidArgumentException::class, 'dot-prefixed')
        ->and(fn (): FileUpload => FileUpload::make('a')->fileTypes(array_fill(0, 11, '.pdf')))->toThrow(InvalidArgumentException::class, 'at most 10');
});

it('defines every permission as a distinct single bit', function (): void {
    $flags = (new ReflectionClass(Permissions::class))->getConstants();

    expect($flags)->toHaveKey('BYPASS_SLOWMODE')
        ->and(array_unique($flags))->toHaveCount(count($flags));

    foreach ($flags as $name => $flag) {
        expect($flag & ($flag - 1))->toBe(0, $name);
    }
});
