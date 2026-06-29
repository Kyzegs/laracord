<?php

declare(strict_types=1);

use Kyzegs\Laracord\Components\ActionRow;
use Kyzegs\Laracord\Components\Button;
use Kyzegs\Laracord\Components\ChannelSelect;
use Kyzegs\Laracord\Components\Modal;
use Kyzegs\Laracord\Components\SelectOption;
use Kyzegs\Laracord\Components\StringSelect;
use Kyzegs\Laracord\Components\TextInput;
use Kyzegs\Laracord\Components\UserSelect;
use Kyzegs\Laracord\Interactions\InteractionResponse;
use Kyzegs\Laracord\Payloads\DiscordMessage;

it('builds a button row', function (): void {
    $row = ActionRow::make(
        Button::primary('vote:yes', 'Yes')->emoji('✅'),
        Button::danger('vote:no', 'No')->disabled(),
        Button::link('https://discord.com', 'Docs'),
    );

    expect($row->toArray())->toBe([
        'type' => 1,
        'components' => [
            ['type' => 2, 'style' => 1, 'label' => 'Yes', 'emoji' => ['name' => '✅'], 'custom_id' => 'vote:yes'],
            ['type' => 2, 'style' => 4, 'label' => 'No', 'custom_id' => 'vote:no', 'disabled' => true],
            ['type' => 2, 'style' => 5, 'label' => 'Docs', 'url' => 'https://discord.com'],
        ],
    ]);
});

it('builds a string select with options', function (): void {
    $select = StringSelect::make('pick')
        ->placeholder('Choose one')
        ->minValues(1)
        ->maxValues(2)
        ->options(
            SelectOption::make('Red', 'red')->description('The red one')->default(),
            SelectOption::make('Blue', 'blue')->emoji('🔵'),
        );

    expect($select->toArray())->toBe([
        'type' => 3,
        'custom_id' => 'pick',
        'placeholder' => 'Choose one',
        'min_values' => 1,
        'max_values' => 2,
        'options' => [
            ['label' => 'Red', 'value' => 'red', 'description' => 'The red one', 'default' => true],
            ['label' => 'Blue', 'value' => 'blue', 'emoji' => ['name' => '🔵']],
        ],
    ]);
});

it('serializes auto-population selects with their type', function (): void {
    expect(UserSelect::make('u')->toArray()['type'])->toBe(5)
        ->and(ChannelSelect::make('c')->channelTypes(0, 2)->toArray())
        ->toBe(['type' => 8, 'custom_id' => 'c', 'channel_types' => [0, 2]]);
});

it('builds a modal with text inputs', function (): void {
    $modal = Modal::make('feedback', 'Send feedback')
        ->text(TextInput::short('subject', 'Subject')->required())
        ->text(TextInput::paragraph('body', 'Body')->maxLength(2000));

    expect($modal->toArray())->toBe([
        'custom_id' => 'feedback',
        'title' => 'Send feedback',
        'components' => [
            ['type' => 1, 'components' => [['type' => 4, 'custom_id' => 'subject', 'style' => 1, 'label' => 'Subject', 'required' => true]]],
            ['type' => 1, 'components' => [['type' => 4, 'custom_id' => 'body', 'style' => 2, 'label' => 'Body', 'max_length' => 2000]]],
        ],
    ]);
});

it('accepts component builders on a message', function (): void {
    $message = (new DiscordMessage)
        ->content('Pick')
        ->components([ActionRow::make(Button::secondary('id', 'Tap'))]);

    expect($message->toArray()['components'])->toBe([
        ['type' => 1, 'components' => [['type' => 2, 'style' => 2, 'label' => 'Tap', 'custom_id' => 'id']]],
    ]);
});

it('returns a modal interaction response from a builder', function (): void {
    $response = InteractionResponse::modal(
        Modal::make('m', 'Title')->text(TextInput::short('field', 'Field')),
    );

    expect($response->getData(true))->toBe([
        'type' => 9,
        'data' => [
            'custom_id' => 'm',
            'title' => 'Title',
            'components' => [
                ['type' => 1, 'components' => [['type' => 4, 'custom_id' => 'field', 'style' => 1, 'label' => 'Field']]],
            ],
        ],
    ]);
});

it('rejects mixing buttons with a select in one row', function (): void {
    ActionRow::make(Button::primary('a', 'A'), UserSelect::make('u'))->toArray();
})->throws(InvalidArgumentException::class);

it('rejects more than five buttons in a row', function (): void {
    ActionRow::make(...array_map(
        static fn (int $i): Button => Button::primary('b'.$i, 'B'.$i),
        range(1, 6),
    ))->toArray();
})->throws(InvalidArgumentException::class);

it('rejects a string select without options', function (): void {
    StringSelect::make('empty')->toArray();
})->throws(InvalidArgumentException::class);
