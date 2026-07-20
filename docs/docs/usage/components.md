# Components

Fluent builders produce Discord's component JSON for legacy messages, Components V2 messages, and modals. Every component implements `Arrayable`/`JsonSerializable`, so you can pass it straight to `DiscordMessage`, `InteractionResponse`, or an endpoint body.

## Buttons

```php
use Kyzegs\Laracord\Components\ActionRow;
use Kyzegs\Laracord\Components\Button;
use Kyzegs\Laracord\Payloads\DiscordMessage;

$message = (new DiscordMessage)
    ->content('Cast your vote')
    ->components([
        ActionRow::make(
            Button::primary('vote:yes', 'Yes')->emoji('✅'),
            Button::danger('vote:no', 'No'),
            Button::link('https://discord.com', 'Docs'),
        ),
    ]);
```

Button factories: `primary`, `secondary`, `success`, `danger` (each takes a `custom_id` and optional label), `link($url, $label)`, and `premium($skuId)`. Chain `->label()`, `->emoji($name, $id?, $animated?)`, and `->disabled()`. An action row holds up to five buttons.

## Select menus

```php
use Kyzegs\Laracord\Components\ChannelSelect;
use Kyzegs\Laracord\Components\SelectOption;
use Kyzegs\Laracord\Components\StringSelect;

ActionRow::make(
    StringSelect::make('color')
        ->placeholder('Pick a color')
        ->minValues(1)
        ->maxValues(2)
        ->options(
            SelectOption::make('Red', 'red')->description('The red one')->default(),
            SelectOption::make('Blue', 'blue')->emoji('🔵'),
        ),
);
```

`UserSelect`, `RoleSelect`, `MentionableSelect`, and `ChannelSelect` are auto-populated menus (no options); `ChannelSelect` additionally accepts `->channelTypes(...)`. A row containing a select menu may not contain anything else.

Selects also support `->required()`. Auto-populated selects accept typed defaults through `->defaultUser($id)`, `->defaultRole($id)`, or `->defaultChannel($id)`, depending on the select type.

## Components V2 messages

Use `componentsV2()` instead of `components()` for Discord's layout and content components. It adds the `IS_COMPONENTS_V2` flag while preserving any other message flags. V2 messages cannot contain legacy `content`, embeds, polls, or stickers, and Laracord rejects those combinations before sending the request.

```php
use Kyzegs\Laracord\Components\ActionRow;
use Kyzegs\Laracord\Components\Button;
use Kyzegs\Laracord\Components\Container;
use Kyzegs\Laracord\Components\MediaGallery;
use Kyzegs\Laracord\Components\MediaGalleryItem;
use Kyzegs\Laracord\Components\Section;
use Kyzegs\Laracord\Components\Separator;
use Kyzegs\Laracord\Components\TextDisplay;
use Kyzegs\Laracord\Components\Thumbnail;
use Kyzegs\Laracord\Payloads\DiscordMessage;

$message = (new DiscordMessage)->componentsV2([
    Container::make(
        Section::make(
            TextDisplay::make('# Release 2.0'),
            TextDisplay::make('Components V2 is ready.'),
        )->accessory(
            Thumbnail::make('https://example.com/release.png')
                ->description('Release artwork'),
        ),
        Separator::make()->spacing(2),
        MediaGallery::make(
            MediaGalleryItem::make('https://example.com/screenshot.png')
                ->description('The new interface'),
        ),
        ActionRow::make(Button::primary('download', 'Download')),
    )->accentColor(0x5865F2),
]);
```

Available V2 message builders are `Section`, `TextDisplay`, `Thumbnail`, `MediaGallery`, `MediaGalleryItem`, `File`, `Separator`, and `Container`. Action rows and buttons also work inside V2 messages. Display an uploaded file by pairing `DiscordMessage::file()` with `File::make($filename)`:

```php
use Kyzegs\Laracord\Components\File as FileComponent;

$message = (new DiscordMessage)
    ->file($stream, 'manual.pdf', 'application/pdf')
    ->componentsV2([FileComponent::make('manual.pdf')]);
```

All components accept an optional API identifier through `->id($id)`. Discord generates unique IDs when they are omitted. Laracord rejects duplicate non-zero `id` and `custom_id` values. A V2 message may contain at most 40 total components; nested components count toward the limit.

## Legacy modals

Modals are returned from an interaction handler. Each text input is wrapped in its own action row by `->text()`.

```php
use Kyzegs\Laracord\Components\Modal;
use Kyzegs\Laracord\Components\TextInput;
use Kyzegs\Laracord\Interactions\InteractionResponse;

InteractionResponse::modal(
    Modal::make('feedback', 'Send feedback')
        ->text(TextInput::short('subject', 'Subject')->required())
        ->text(TextInput::paragraph('body', 'Body')->maxLength(2000)),
);
```

Builders validate Discord's limits (label/`custom_id` lengths, option and row counts) and throw `InvalidArgumentException` when exceeded.

## Modern modals

Discord recommends `Label` instead of action rows for modal fields. `Modal::label()` creates the wrapper directly; `Modal::add()` also accepts a `Label` or top-level `TextDisplay`.

```php
use Kyzegs\Laracord\Components\Checkbox;
use Kyzegs\Laracord\Components\CheckboxGroup;
use Kyzegs\Laracord\Components\FileUpload;
use Kyzegs\Laracord\Components\RadioGroup;
use Kyzegs\Laracord\Components\SelectOption;
use Kyzegs\Laracord\Components\TextDisplay;

$modal = Modal::make('report', 'Submit report')
    ->add(TextDisplay::make('Fields marked required must be completed.'))
    ->label(
        'Details',
        TextInput::paragraph('details')->required()->maxLength(4000),
        'Describe what happened.',
    )
    ->label('Evidence', FileUpload::make('evidence')->maxValues(3))
    ->label('Priority', RadioGroup::make('priority')->options(
        SelectOption::make('Normal', 'normal')->default(),
        SelectOption::make('Urgent', 'urgent'),
    ))
    ->label('Notifications', CheckboxGroup::make('notifications')->options(
        SelectOption::make('Email', 'email'),
        SelectOption::make('Discord', 'discord'),
    )->minValues(0)->required(false))
    ->label('Remember my choices', Checkbox::make('remember')->default());

InteractionResponse::modal($modal);
```

Modern modal controls are `FileUpload`, `RadioGroup`, `CheckboxGroup`, and `Checkbox`. Radio and checkbox groups reuse `SelectOption` because Discord gives these options the same label, value, description, and default fields.

See Discord's [component reference](https://docs.discord.com/developers/components/reference) for placement rules and rendered behavior.
