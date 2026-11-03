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

The provider rejects a callback whose `iss` parameter is missing or is not `https://discord.com`, which protects apps that support more than one OAuth2 provider from having a code from another one exchanged here. Discord sends `iss` on every redirect, so if your frontend forwards the callback to your backend, forward `iss` along with `code` and `state`.

Clients are immutable. Switch context without mutating shared state:

```php
$bot = $discord->asBot();
$user = $bot->asUser($accessToken);
$public = $bot->withoutAuthentication();
```

Rate-limit buckets are isolated by one-way credential fingerprint. Tokens and webhook secrets are redacted from cache keys, logs, and exception URLs.
