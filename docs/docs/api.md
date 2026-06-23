# API Reference

Laracord groups Discord HTTP routes into resource clients. See the generated [endpoint catalog](./api/endpoints.md).

```php
$response = Laracord::bot()->guilds()->call(
    'getGuild',
    ['guild_id' => $guildId],
    query: ['with_counts' => true],
);
```

Use `DiscordClient::send(DiscordRequest)` for a newly released Discord route not yet present in the catalog.
