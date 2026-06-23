# Migrating from 0.x

Laracord 1.0 intentionally removes flat `Client` methods and integer ID signatures.

```php
// Before 1.0
Laracord::createMessage(123, ['content' => 'Hello']);

// 1.x
Laracord::bot()->messages()->create(
    ['channel_id' => '123'],
    ['content' => 'Hello'],
);
```

- Use string snowflakes.
- Read results through `DiscordResponse::json()`.
- Handle package-specific HTTP exceptions.
- Use `bot()`, `bearer()`, or `withoutAuthentication()` explicitly.
- Replace bot-backed notification delivery with Discord webhook routing.
