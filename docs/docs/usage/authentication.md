# Authentication

Laracord uses Discord's Bot Token authentication to interact with the Discord API. This guide covers how authentication works and how to set it up properly.

## How Authentication Works

Laracord uses Discord's Bot Token authentication, which is the standard method for Discord bots. The bot token is automatically included in all API requests as an Authorization header.

### Automatic Token Handling

Laracord automatically handles token authentication for you:

1. **Environment Variable**: Reads the token from `DISCORD_BOT_TOKEN`
2. **Request Headers**: Automatically adds the `Authorization: Bot <token>` header
3. **Error Handling**: Provides clear error messages for authentication issues

## Setting Up Authentication

### 1. Get Your Bot Token

1. Go to the [Discord Developer Portal](https://discord.com/developers/applications)
2. Select your application
3. Go to the "Bot" section
4. Click "Reset Token" to generate a new token
5. Copy the token (you won't be able to see it again)

### 2. Configure Your Environment

Add the token to your `.env` file:

```env
DISCORD_BOT_TOKEN=your_bot_token_here
```

### 3. Verify Authentication

Test your authentication with a simple command:

```php
use Kyzegs\Laracord\Facades\Laracord;

try {
    $user = Laracord::getCurrentUser();
    echo "Authenticated as: " . $user['username'];
} catch (Exception $e) {
    echo "Authentication failed: " . $e->getMessage();
}
```

## Token Security

### Best Practices

- **Never commit tokens to version control**
- Use environment variables for all tokens
- Rotate tokens regularly
- Use different tokens for development and production
- Store tokens securely (use Laravel's encryption if needed)

### Environment-Specific Tokens

```env
# Development
DISCORD_BOT_TOKEN=dev_token_here

# Production
DISCORD_BOT_TOKEN=prod_token_here
```

### Token Rotation

If you need to rotate your bot token:

1. Generate a new token in the Discord Developer Portal
2. Update your environment variables
3. Restart your application
4. Verify the new token works

## Error Handling

### Common Authentication Errors

**401 Unauthorized**
```php
try {
    $result = Laracord::getCurrentUser();
} catch (HttpException $e) {
    if ($e->getStatusCode() === 401) {
        // Token is invalid or expired
        Log::error('Invalid Discord bot token');
    }
}
```

**403 Forbidden**
```php
try {
    $result = Laracord::createMessage($channelId, $data);
} catch (HttpException $e) {
    if ($e->getStatusCode() === 403) {
        // Bot lacks required permissions
        Log::error('Bot lacks required permissions');
    }
}
```

### Token Validation

You can validate your token before making requests:

```php
use Kyzegs\Laracord\Facades\Laracord;

function validateDiscordToken(): bool
{
    try {
        Laracord::getCurrentUser();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if (!validateDiscordToken()) {
    throw new Exception('Invalid Discord bot token');
}
```

## Advanced Authentication

### Custom HTTP Client

You can customize the HTTP client for advanced authentication scenarios:

```php
use GuzzleHttp\Client;
use Kyzegs\Laracord\Client as LaracordClient;

// Custom client with additional headers
$guzzleClient = new Client([
    'headers' => [
        'User-Agent' => 'MyApp/1.0',
        'X-Custom-Header' => 'value',
    ],
]);

$laracordClient = new LaracordClient($guzzleClient);
```

### Multiple Bot Support

For applications using multiple bots:

```php
// In a service provider
$this->app->singleton('laracord.primary', function ($app) {
    $client = new Client([
        'headers' => [
            'Authorization' => 'Bot ' . env('DISCORD_BOT_TOKEN'),
        ],
    ]);
    return new LaracordClient($client);
});

$this->app->singleton('laracord.secondary', function ($app) {
    $client = new Client([
        'headers' => [
            'Authorization' => 'Bot ' . env('DISCORD_SECONDARY_BOT_TOKEN'),
        ],
    ]);
    return new LaracordClient($client);
});
```

## Troubleshooting

### Common Issues

**"Invalid token" errors:**
- Verify the token is correct and complete
- Check for extra spaces or characters
- Ensure the token hasn't been regenerated

**"Missing token" errors:**
- Verify `DISCORD_BOT_TOKEN` is set in your `.env`
- Check that the environment variable is being loaded
- Clear your application cache: `php artisan config:clear`

**"Token expired" errors:**
- Generate a new token in the Discord Developer Portal
- Update your environment variables
- Restart your application

### Debugging

Enable debug logging to troubleshoot authentication issues:

```php
// In your config/logging.php
'channels' => [
    'discord' => [
        'driver' => 'single',
        'path' => storage_path('logs/discord.log'),
        'level' => 'debug',
    ],
],
```

## Next Steps

1. [Learn about making API requests](./making-requests.md)
2. [Understand error handling](./error-handling.md)
3. [Explore the API reference](../api.md) 
