# Application Commands

Laracord ships Artisan commands to manage your bot's slash/application commands. Define them in `config/laracord.php` under `commands` as Discord command objects:

```php
// config/laracord.php
'commands' => [
    [
        'name' => 'ping',
        'description' => 'Replies with pong',
    ],
    [
        'name' => 'ban',
        'description' => 'Ban a member',
        'options' => [
            ['name' => 'user', 'description' => 'Member to ban', 'type' => 6, 'required' => true],
        ],
    ],
],
```

Set `DISCORD_APPLICATION_ID` so the commands know which application to target.

## Syncing

`laracord:commands:sync` bulk-overwrites Discord with the config definitions. Without options it targets global commands; pass `--guild` to scope to a single guild (guild commands update instantly, which is convenient while developing):

```bash
php artisan laracord:commands:sync
php artisan laracord:commands:sync --guild=123456789012345678
```

## Listing

`laracord:commands:list` prints the commands currently registered with Discord:

```bash
php artisan laracord:commands:list
php artisan laracord:commands:list --guild=123456789012345678
```

## Clearing

`laracord:commands:clear` removes every registered command:

```bash
php artisan laracord:commands:clear
php artisan laracord:commands:clear --guild=123456789012345678
```

Pair this with the [interaction router](./interactions.md) to handle the commands once Discord invokes them.
