# Authentication

## Bot context

Set `DISCORD_BOT_TOKEN`, then use:

```php
$discord = Laracord::bot();
```

## OAuth bearer context

```php
$socialiteUser = Socialite::driver('discord')->user();
$discord = Laracord::bearer($socialiteUser->accessToken());
```

Clients are immutable. Switch context without mutating shared state:

```php
$bot = $discord->asBot();
$user = $bot->asUser($accessToken);
$public = $bot->withoutAuthentication();
```

Rate-limit buckets are isolated by one-way credential fingerprint. Tokens and webhook secrets are redacted from cache keys, logs, and exception URLs.
