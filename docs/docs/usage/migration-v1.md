# Migrating from 0.x

Laracord 1.0 is a full rewrite of the HTTP layer. The 0.x surface offered two calling
styles: flat facade methods (`Laracord::getChannel($channelId)`) and a low-level
`Client\Http` facade whose URLs were built from `Constants\Routes` constants with
`sprintf()`. 1.0 replaces both with an authentication context (`bot()`, `bearer()`,
`withoutAuthentication()`), typed resource clients, and a `DiscordResponse` object.

```php
// 0.x
use Kyzegs\Laracord\Facades\Laracord;

Laracord::createMessage($channelId, ['content' => 'Hello']);

// or, low-level
use Kyzegs\Laracord\Client\Http;
use Kyzegs\Laracord\Constants\Routes;

Http::post(sprintf(Routes::CREATE_GLOBAL_APPLICATION_COMMAND, $appId), ['name' => 'ping']);

// 1.x
use Kyzegs\Laracord\Facades\Laracord;

Laracord::bot()->messages()->create(['channel_id' => $channelId], ['content' => 'Hello']);
Laracord::bot()->commands()->createGlobalCommand(['application_id' => $appId], ['name' => 'ping']);
```

What changed:

- Use string snowflakes instead of integers — never pass IDs as `int`.
- Select an auth context explicitly: `bot()`, `bearer()`, or `withoutAuthentication()`.
- Route parameters move from positional `sprintf()` values into a named parameter array.
- Read results through `DiscordResponse::json()` rather than `Illuminate\Http\Client\Response`.
- Handle the package-specific HTTP exceptions (see [Error Handling](./error-handling.md)).
- The service provider was renamed from `Kyzegs\Laracord\LaracordServiceProvider` to
  `Kyzegs\Laracord\ServiceProvider` (auto-discovered; only matters if you registered it by hand).
- Replace bot-backed notification delivery with Discord webhook routing.

## Automated upgrade with Rector

Laracord ships a [Rector](https://getrector.com) set that rewrites both 0.x calling styles
— flat facade methods and `Http` + `Routes` calls — into the 1.0 resource-client API, and
renames the service provider.

Install Rector if you do not already have it:

```bash
composer require rector/rector --dev
```

Add the set to your `rector.php`:

```php
use Kyzegs\Laracord\Rector\LaracordSetList;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([__DIR__.'/app'])
    ->withSets([
        LaracordSetList::UPGRADE_0_TO_1,
    ]);
```

Preview the changes, then apply them:

```bash
vendor/bin/rector process --dry-run
vendor/bin/rector process
```

### What the set rewrites

The `HttpFacadeCallToLaracordRector` rule covers the **entire** 0.x endpoint surface, in both
calling styles. It carries the full list of 0.x endpoint names and maps each one onto the 1.0
[endpoint catalog](../api/endpoints.md) by its underlying Discord method and path — the one
identity that does not change. That means endpoints renamed in 1.0 are handled too: the flat
0.x `getChannel` becomes `channels()->get()`, `createMessage` becomes `messages()->create()`,
and so on.

For each matched call:

- the 0.x name (or `Routes::` constant) resolves to the canonical 1.0 resource and method;
- positional ID arguments map, in order, onto the route's named placeholders;
- the trailing argument becomes the request body, except for GET endpoints where it becomes
  the query (passed as the third argument, with a `null` body);
- placeholder-free routes (e.g. `getCurrentUser`) become argument-less calls.

```php
// before — flat facade
Laracord::getChannel($channelId);
Laracord::createMessage($channelId, ['content' => 'Hello']);
Laracord::getGuild($guildId, ['with_counts' => true]);

// before — low-level Http + Routes
Http::patch(sprintf(Routes::EDIT_GUILD_APPLICATION_COMMAND, $appId, $guildId, $commandId), ['description' => 'updated']);
Http::get(sprintf(Routes::GET_GUILD_AUDIT_LOG, $guildId));

// after
Laracord::bot()->channels()->get(['channel_id' => $channelId]);
Laracord::bot()->messages()->create(['channel_id' => $channelId], ['content' => 'Hello']);
Laracord::bot()->guilds()->get(['guild_id' => $guildId], null, ['with_counts' => true]);
Laracord::bot()->commands()->editGuildApplicationCommand(
    ['application_id' => $appId, 'guild_id' => $guildId, 'command_id' => $commandId],
    ['description' => 'updated'],
);
Laracord::bot()->auditLogs()->getGuildAuditLog(['guild_id' => $guildId]);
```

The set also renames `Kyzegs\Laracord\LaracordServiceProvider` to `Kyzegs\Laracord\ServiceProvider`.

### What Rector cannot do for you

The set is deliberately conservative — it only rewrites calls it can transform into valid 1.0
code. Review these manually after running it:

- **Authentication context.** Rewritten calls default to `bot()`. If a request used
  `Http::withToken($token)` (a bearer token) or `Http::withoutToken()`, switch it to
  `Laracord::bearer($token)` or `Laracord::withoutAuthentication()`.
- **Response handling.** Calls now return `DiscordResponse`. Replace `->json()['field']` style
  access from Laravel's response with `$response->json('field')`, and update error handling to
  catch Laracord's exceptions.
- **Raw URLs.** Any 0.x call that passed a hand-built URL string instead of a `Routes` constant
  is left untouched; port it to the matching resource method from the
  [endpoint catalog](../api/endpoints.md).
- **Leftover imports.** The old `use Kyzegs\Laracord\Client\Http;` and
  `use Kyzegs\Laracord\Constants\Routes;` imports are no longer used after the rewrite. Enable
  Rector's `->withImportNames(removeUnusedImports: true)` or remove them by hand.
