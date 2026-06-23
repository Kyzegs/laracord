# Installation

## Requirements

- PHP 8.2+
- Laravel 12 or 13
- `ext-json` and `ext-sodium`
- A Discord bot token for bot-authenticated requests

## Install the package

```bash
composer require kyzegs/laracord:^1.0@rc
```

Laravel discovers the service provider automatically. Publish the configuration when you need to change its defaults:

```bash
php artisan vendor:publish --tag=laracord-config
```

Add your bot token to `.env`:

```dotenv
DISCORD_BOT_TOKEN=your_discord_bot_token_here
```

## Verify the installation

Open Tinker:

```bash
php artisan tinker
```

Then request the current bot user:

```php
use Kyzegs\Laracord\Facades\Laracord;

$response = Laracord::bot()->users()->getCurrentUser();
$response->json();
```

Laracord returns a `DiscordResponse`, so response data is available through `json()` rather than directly as an array.

## Next steps

1. [Review the configuration](./configuration.md)
2. [Learn about authentication](../usage/authentication.md)
3. [Make API requests](../usage/making-requests.md)
