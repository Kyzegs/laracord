# Installation

This guide will walk you through installing Laracord in your Laravel application.

## Prerequisites

Before installing Laracord, make sure you have:

- PHP 8.1 or higher
- Laravel 10 or higher
- Composer installed
- A Discord bot token (see [Discord Developer Portal](https://discord.com/developers/applications))

## Installation Steps

### 1. Install via Composer

```bash
composer require kyzegs/laracord
```

### 2. Publish Configuration (Optional)

Laracord will work out of the box, but you can publish the configuration file to customize settings:

```bash
php artisan vendor:publish --provider="Kyzegs\Laracord\ServiceProvider"
```

This will create a `config/laracord.php` file in your application.

### 3. Set Environment Variables

Add your Discord bot token to your `.env` file:

```env
DISCORD_BOT_TOKEN=your_discord_bot_token_here
```

### 4. Verify Installation

You can verify that Laracord is properly installed by running:

```bash
php artisan tinker
```

Then test the facade:

```php
use Kyzegs\Laracord\Facades\Laracord;
Laracord::getCurrentUser();
```

If everything is set up correctly, this should return your bot's user information.

## Configuration

### Basic Configuration

The default configuration should work for most use cases. If you published the config file, you can customize these settings:

```php
// config/laracord.php
return [
    'token' => env('DISCORD_BOT_TOKEN'),
    'base_url' => 'https://discord.com/api/v10',
    'timeout' => 30,
    'retry_attempts' => 3,
    'retry_delay' => 1,
];
```

### Advanced Configuration

For advanced use cases, you can customize:

- **Base URL**: Change the Discord API base URL (useful for testing)
- **Timeout**: Adjust request timeout values
- **Retry Logic**: Configure retry attempts and delays
- **Middleware**: Add custom middleware for request/response processing

## Next Steps

1. [Configure your Discord bot](./configuration.md)
2. [Learn about authentication](../usage/authentication.md)
3. [Start making API calls](../usage/making-requests.md)

## Troubleshooting

### Common Issues

**"Class 'Kyzegs\Laracord\ServiceProvider' not found"**
- Make sure you've installed the package via Composer
- Clear your application cache: `php artisan config:clear`

**"Invalid token" errors**
- Verify your Discord bot token is correct
- Ensure the token is properly set in your `.env` file
- Check that your bot has the necessary permissions

**Rate limiting errors**
- Laracord handles rate limiting automatically
- If you're still getting rate limited, check your bot's usage patterns

### Getting Help

If you're still having issues:

1. Check the [GitHub issues](https://github.com/kyzegs/laracord/issues) for known problems
2. Create a new issue with detailed information about your setup
3. Join our Discord community (coming soon) 
