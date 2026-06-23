# Configuration

Publish `config/laracord.php` when you need to override Laracord's defaults:

```bash
php artisan vendor:publish --tag=laracord-config
```

## Discord credentials and API

```dotenv
DISCORD_BOT_TOKEN=your_discord_bot_token
DISCORD_PUBLIC_KEY=your_application_public_key
DISCORD_APPLICATION_ID=your_application_id
DISCORD_API_URL=https://discord.com/api
DISCORD_API_VERSION=10
DISCORD_USER_AGENT="DiscordBot (https://example.com, 1.0.0)"
```

`DISCORD_BOT_TOKEN` is required only for bot-authenticated calls. The public key is used to verify interaction and webhook-event signatures.

## OAuth2

```dotenv
DISCORD_CLIENT_ID=your_client_id
DISCORD_CLIENT_SECRET=your_client_secret
DISCORD_REDIRECT_URI=/callback
DISCORD_SCOPES=identify,email,guilds
```

The comma-separated scopes configure the included Socialite provider.

## HTTP transport

```dotenv
LARACORD_HTTP_TIMEOUT=30
LARACORD_CONNECT_TIMEOUT=10
LARACORD_SERVER_RETRIES=5
```

Laracord retries connection failures and Discord responses with status `500`, `502`, `504`, or `524`. `LARACORD_SERVER_RETRIES` is the total number of attempts, including the first request.

## Rate limiting

```dotenv
LARACORD_CACHE_PREFIX=laracord:rate-limit:
LARACORD_LOCK_PREFIX=laracord:lock:
LARACORD_RATE_LIMIT_RETRIES=5
LARACORD_RATE_LIMIT_BUFFER=0.25
LARACORD_RATE_LIMIT_JITTER=2.0
LARACORD_MAX_RATE_LIMIT_DELAY=
LARACORD_GLOBAL_REQUESTS=50
LARACORD_INVALID_REQUESTS=9000
LARACORD_LOCK_TTL=60
LARACORD_LOCK_WAIT=10
```

Use a shared cache store with atomic locks, such as Redis, when multiple workers make Discord requests. The complete defaults live under `laracord.rate_limit` in the published configuration.

## Signature verification

```dotenv
LARACORD_SIGNATURE_MAX_AGE=300
```

This controls how old an interaction or webhook-event timestamp may be when `VerifyDiscordSignature` validates it.

## Test the configuration

```php
use Kyzegs\Laracord\Facades\Laracord;

$user = Laracord::bot()
    ->users()
    ->getCurrentUser()
    ->json();
```

Keep bot tokens and OAuth secrets out of version control. Laracord fingerprints credentials for rate-limit isolation without putting their plaintext values in cache keys.
