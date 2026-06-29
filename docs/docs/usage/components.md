# Message Components

Fluent builders produce the Discord component JSON for buttons, select menus, text inputs, and modals. Every builder implements `Arrayable`/`JsonSerializable`, so you can pass them straight to `DiscordMessage::components()`, `InteractionResponse`, or any endpoint body.

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

## Modals

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
