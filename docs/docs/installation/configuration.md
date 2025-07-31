# Configuration

This guide covers all configuration options available in Laracord.

## Environment Variables

Laracord uses the following environment variables:

```env
# Required: Your Discord bot token
DISCORD_BOT_TOKEN=your_discord_bot_token_here

# Optional: Custom base URL (useful for testing)
LARACORD_BASE_URL=https://discord.com/api/v10

# Optional: Request timeout in seconds
LARACORD_TIMEOUT=30

# Optional: Number of retry attempts
LARACORD_RETRY_ATTEMPTS=3

# Optional: Delay between retries in seconds
LARACORD_RETRY_DELAY=1
```

## Configuration File

If you published the configuration file, you can customize these settings in `config/laracord.php`:

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Discord Bot Token
    |--------------------------------------------------------------------------
    |
    | Your Discord bot token. You can get this from the Discord Developer Portal.
    |
    */
    'token' => env('DISCORD_BOT_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Discord API Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for Discord's API. Change this for testing or custom endpoints.
    |
    */
    'base_url' => env('LARACORD_BASE_URL', 'https://discord.com/api/v10'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout for HTTP requests in seconds.
    |
    */
    'timeout' => env('LARACORD_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Configure automatic retry behavior for failed requests.
    |
    */
    'retry_attempts' => env('LARACORD_RETRY_ATTEMPTS', 3),
    'retry_delay' => env('LARACORD_RETRY_DELAY', 1),

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Custom middleware classes to process requests and responses.
    |
    */
    'middleware' => [
        // Add your custom middleware classes here
    ],
];
```

## Discord Bot Setup

### Creating a Discord Bot

1. Go to the [Discord Developer Portal](https://discord.com/developers/applications)
2. Click "New Application"
3. Give your application a name
4. Go to the "Bot" section
5. Click "Add Bot"
6. Copy the bot token (you'll need this for your `.env` file)

### Bot Permissions

Your bot will need appropriate permissions based on what you want to do:

**Basic Permissions:**
- Send Messages
- Read Message History
- Use Slash Commands

**Advanced Permissions:**
- Manage Channels
- Manage Roles
- Ban Members
- Kick Members

### Inviting Your Bot

1. Go to the "OAuth2" section in your Discord application
2. Select "bot" under "Scopes"
3. Select the permissions your bot needs
4. Copy the generated URL and visit it to invite your bot to your server

## Advanced Configuration

### Custom HTTP Client

You can customize the underlying HTTP client by binding a custom implementation:

```php
// In a service provider
use GuzzleHttp\Client;
use Kyzegs\Laracord\Client as LaracordClient;

$this->app->singleton(LaracordClient::class, function ($app) {
    $guzzleClient = new Client([
        'timeout' => 60,
        'headers' => [
            'User-Agent' => 'MyApp/1.0',
        ],
    ]);
    
    return new LaracordClient($guzzleClient);
});
```

### Custom Middleware

You can add custom middleware to process requests and responses:

```php
// Create a custom middleware
class LoggingMiddleware
{
    public function handle($request, $next)
    {
        Log::info('Discord API Request', [
            'method' => $request->getMethod(),
            'url' => $request->getUrl(),
        ]);
        
        $response = $next($request);
        
        Log::info('Discord API Response', [
            'status' => $response->getStatusCode(),
        ]);
        
        return $response;
    }
}

// Register it in your config
'middleware' => [
    LoggingMiddleware::class,
],
```

### Rate Limiting

Laracord handles rate limiting automatically, but you can customize the behavior:

```php
// In your config
'rate_limiting' => [
    'enabled' => true,
    'max_requests_per_minute' => 50,
    'backoff_multiplier' => 2,
],
```

## Testing Configuration

You can test your configuration with a simple command:

```bash
php artisan tinker
```

```php
use Kyzegs\Laracord\Facades\Laracord;

// Test basic connectivity
$user = Laracord::getCurrentUser();
dd($user);
```

## Security Considerations

### Token Security

- **Never commit your bot token to version control**
- Use environment variables for all sensitive data
- Rotate your bot token regularly
- Use different tokens for development and production

### Rate Limiting

- Be mindful of Discord's rate limits
- Laracord handles this automatically, but monitor your usage
- Consider implementing additional rate limiting for high-traffic applications

### Error Handling

- Always handle potential API errors gracefully
- Log errors for debugging
- Implement fallback strategies for critical operations

## Next Steps

1. [Learn about authentication](../usage/authentication.md)
2. [Start making API calls](../usage/making-requests.md)
3. [Explore the API reference](../../api.md) 
