<?php

declare(strict_types=1);

use Kyzegs\Laracord\Components\ActionRow;
use Kyzegs\Laracord\Components\Button;
use Kyzegs\Laracord\Components\Checkbox;
use Kyzegs\Laracord\Components\CheckboxGroup;
use Kyzegs\Laracord\Components\Container;
use Kyzegs\Laracord\Components\File as ComponentFile;
use Kyzegs\Laracord\Components\FileUpload;
use Kyzegs\Laracord\Components\MediaGallery;
use Kyzegs\Laracord\Components\MediaGalleryItem;
use Kyzegs\Laracord\Components\Modal;
use Kyzegs\Laracord\Components\RadioGroup;
use Kyzegs\Laracord\Components\Section;
use Kyzegs\Laracord\Components\SelectOption;
use Kyzegs\Laracord\Components\Separator;
use Kyzegs\Laracord\Components\TextDisplay;
use Kyzegs\Laracord\Components\TextInput;
use Kyzegs\Laracord\Components\Thumbnail;
use Kyzegs\Laracord\Components\UserSelect;
use Kyzegs\Laracord\Payloads\DiscordMessage;

it('builds every Components V2 message component and enables the flag', function (): void {
    $message = (new DiscordMessage)->componentsV2([
        ActionRow::make(Button::primary('accept', 'Accept')),
        Section::make(TextDisplay::make('A section')->id(2))
            ->id(1)
            ->accessory(Thumbnail::make('https://example.com/thumb.png')->description('Preview')->spoiler()),
        TextDisplay::make('Standalone text'),
        MediaGallery::make(
            MediaGalleryItem::make('https://example.com/one.png')->description('First image'),
            MediaGalleryItem::make('attachment://two.png')->spoiler(),
        ),
        ComponentFile::make('manual.pdf')->spoiler(),
        Separator::make()->divider(false)->spacing(2),
        Container::make(TextDisplay::make('Inside'))->accentColor(0x5865F2)->spoiler(),
    ]);

    expect($message->toArray())->toBe([
        'allowed_mentions' => ['parse' => []],
        'flags' => 32768,
        'components' => [
            ['type' => 1, 'components' => [['type' => 2, 'style' => 1, 'label' => 'Accept', 'custom_id' => 'accept']]],
            [
                'type' => 9,
                'id' => 1,
                'components' => [['type' => 10, 'id' => 2, 'content' => 'A section']],
                'accessory' => [
                    'type' => 11,
                    'media' => ['url' => 'https://example.com/thumb.png'],
                    'description' => 'Preview',
                    'spoiler' => true,
                ],
            ],
            ['type' => 10, 'content' => 'Standalone text'],
            [
                'type' => 12,
                'items' => [
                    ['media' => ['url' => 'https://example.com/one.png'], 'description' => 'First image'],
                    ['media' => ['url' => 'attachment://two.png'], 'spoiler' => true],
                ],
            ],
            ['type' => 13, 'file' => ['url' => 'attachment://manual.pdf'], 'spoiler' => true],
            ['type' => 14, 'divider' => false, 'spacing' => 2],
            [
                'type' => 17,
                'accent_color' => 0x5865F2,
                'components' => [['type' => 10, 'content' => 'Inside']],
                'spoiler' => true,
            ],
        ],
    ]);
});

it('builds modern modal layouts and controls', function (): void {
    $modal = Modal::make('character', 'Create character')
        ->add(TextDisplay::make('Complete every field.'))
        ->label('Biography', TextInput::paragraph('bio')->maxLength(4000), 'Markdown is supported.')
        ->label('Evidence', FileUpload::make('evidence')->minValues(0)->maxValues(3)->required(false))
        ->label('Class', RadioGroup::make('class')->options(
            SelectOption::make('Warrior', 'warrior')->description('Strong')->default(),
            SelectOption::make('Wizard', 'wizard')->description('Smart'),
        ))
        ->label('Options', CheckboxGroup::make('options')->options(
            SelectOption::make('Email me', 'email'),
            SelectOption::make('Publish', 'publish'),
        )->minValues(0)->maxValues(2)->required(false))
        ->label('Remember', Checkbox::make('remember')->default());

    expect($modal->toArray())->toBe([
        'custom_id' => 'character',
        'title' => 'Create character',
        'components' => [
            ['type' => 10, 'content' => 'Complete every field.'],
            [
                'type' => 18,
                'label' => 'Biography',
                'description' => 'Markdown is supported.',
                'component' => ['type' => 4, 'custom_id' => 'bio', 'style' => 2, 'max_length' => 4000],
            ],
            [
                'type' => 18,
                'label' => 'Evidence',
                'component' => ['type' => 19, 'custom_id' => 'evidence', 'min_values' => 0, 'max_values' => 3, 'required' => false],
            ],
            [
                'type' => 18,
                'label' => 'Class',
                'component' => [
                    'type' => 21,
                    'custom_id' => 'class',
                    'options' => [
                        ['label' => 'Warrior', 'value' => 'warrior', 'description' => 'Strong', 'default' => true],
                        ['label' => 'Wizard', 'value' => 'wizard', 'description' => 'Smart'],
                    ],
                ],
            ],
            [
                'type' => 18,
                'label' => 'Options',
                'component' => [
                    'type' => 22,
                    'custom_id' => 'options',
                    'options' => [
                        ['label' => 'Email me', 'value' => 'email'],
                        ['label' => 'Publish', 'value' => 'publish'],
                    ],
                    'min_values' => 0,
                    'max_values' => 2,
                    'required' => false,
                ],
            ],
            [
                'type' => 18,
                'label' => 'Remember',
                'component' => ['type' => 23, 'custom_id' => 'remember', 'default' => true],
            ],
        ],
    ]);
});

it('supports ids, required selects, and typed default values', function (): void {
    expect(UserSelect::make('users')->id(7)->defaultUser('123')->required(false)->toArray())->toBe([
        'type' => 5,
        'id' => 7,
        'custom_id' => 'users',
        'required' => false,
        'default_values' => [['id' => '123', 'type' => 'user']],
    ]);
});

it('preserves message flags and rejects duplicate component identifiers', function (): void {
    expect((new DiscordMessage)
        ->flags(64)
        ->componentsV2([TextDisplay::make('Ephemeral V2')])
        ->toArray()['flags'])
        ->toBe(32832)
        ->and(fn (): array => (new DiscordMessage)->componentsV2([
            ActionRow::make(Button::primary('duplicate', 'One')),
            ActionRow::make(Button::secondary('duplicate', 'Two')),
        ])->toArray())
        ->toThrow(InvalidArgumentException::class)
        ->and(fn (): array => Modal::make('duplicate-modal', 'Duplicate modal')
            ->label('One', TextInput::short('duplicate'))
            ->label('Two', TextInput::short('duplicate'))
            ->toArray())
        ->toThrow(InvalidArgumentException::class);
});

it('rejects incompatible fields and component limits on V2 messages', function (): void {
    expect(fn (): array => (new DiscordMessage)
        ->content('Legacy content')
        ->componentsV2([TextDisplay::make('V2 content')])
        ->toArray())
        ->toThrow(InvalidArgumentException::class)
        ->and(fn (): array => (new DiscordMessage)
            ->componentsV2(array_map(
                static fn (int $id): TextDisplay => TextDisplay::make((string) $id),
                range(1, 41),
            ))
            ->toArray())
        ->toThrow(InvalidArgumentException::class);
});

it('rejects invalid V2 child configurations', function (): void {
    expect(fn (): array => Section::make(TextDisplay::make('Missing accessory'))->toArray())
        ->toThrow(InvalidArgumentException::class)
        ->and(fn (): array => RadioGroup::make('choice')->option(SelectOption::make('Only', 'one'))->toArray())
        ->toThrow(InvalidArgumentException::class)
        ->and(fn (): array => CheckboxGroup::make('choices')
            ->option(SelectOption::make('Only', 'one'))
            ->maxValues(2)
            ->toArray())
        ->toThrow(InvalidArgumentException::class);
});
