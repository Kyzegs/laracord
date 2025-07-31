# Making Requests

This guide covers how to make API requests using Laracord.

## Basic Usage

Laracord provides a simple facade interface for making Discord API requests:

```php
use Kyzegs\Laracord\Facades\Laracord;

// Get a channel
$channel = Laracord::getChannel(123456789);

// Create a message
$message = Laracord::createMessage(123456789, [
    'content' => 'Hello, Discord!'
]);
```

## Request Types

### GET Requests

For retrieving data:

```php
// Get current user
$user = Laracord::getCurrentUser();

// Get a guild
$guild = Laracord::getGuild(987654321);

// Get channel messages
$messages = Laracord::getChannelMessages(123456789);
```

### POST Requests

For creating new resources:

```php
// Create a message
$message = Laracord::createMessage($channelId, [
    'content' => 'Hello, Discord!',
    'embeds' => [
        [
            'title' => 'My Embed',
            'description' => 'This is an embed message'
        ]
    ]
]);

// Create a webhook
$webhook = Laracord::createWebhook($channelId, [
    'name' => 'My Webhook'
]);
```

### PATCH Requests

For updating existing resources:

```php
// Edit a message
$updatedMessage = Laracord::editMessage($channelId, $messageId, [
    'content' => 'Updated message content'
]);

// Modify a guild
$updatedGuild = Laracord::modifyGuild($guildId, [
    'name' => 'New Guild Name'
]);
```

### DELETE Requests

For removing resources:

```php
// Delete a message
Laracord::deleteMessage($channelId, $messageId);

// Delete a webhook
Laracord::deleteWebhook($webhookId);
```

## Error Handling

Always handle potential errors when making requests:

```php
try {
    $channel = Laracord::getChannel($channelId);
    echo "Channel name: " . $channel['name'];
} catch (HttpException $e) {
    if ($e->getStatusCode() === 404) {
        echo "Channel not found";
    } elseif ($e->getStatusCode() === 403) {
        echo "Bot lacks permissions to view this channel";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
```

## Rate Limiting

Laracord automatically handles Discord's rate limits, but you should be mindful of your usage:

```php
// Good: Batch operations when possible
$messages = Laracord::getChannelMessages($channelId, [
    'limit' => 100
]);

// Avoid: Making too many requests too quickly
for ($i = 0; $i < 1000; $i++) {
    Laracord::getChannel($channelId); // This will hit rate limits
}
```

## Best Practices

### 1. Validate Input

```php
// Always validate IDs before making requests
if (!is_numeric($channelId) || $channelId <= 0) {
    throw new InvalidArgumentException('Invalid channel ID');
}

$channel = Laracord::getChannel($channelId);
```

### 2. Use Proper Data Structures

```php
// Good: Use proper array structure for embeds
$message = Laracord::createMessage($channelId, [
    'content' => 'Hello!',
    'embeds' => [
        [
            'title' => 'My Title',
            'description' => 'My Description',
            'color' => 0x5865f2
        ]
    ]
]);
```

### 3. Handle Large Responses

```php
// For large responses, consider pagination
$messages = Laracord::getChannelMessages($channelId, [
    'limit' => 50,
    'before' => $lastMessageId
]);
```

### 4. Cache When Appropriate

```php
// Cache frequently accessed data
$cacheKey = "guild_{$guildId}";
$guild = Cache::remember($cacheKey, 300, function () use ($guildId) {
    return Laracord::getGuild($guildId);
});
```

## Advanced Usage

### Custom Headers

You can customize the HTTP client for special requirements:

```php
use GuzzleHttp\Client;
use Kyzegs\Laracord\Client as LaracordClient;

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
```

## Troubleshooting

### Common Issues

**"Invalid snowflake" errors:**
- Ensure IDs are valid Discord snowflakes
- Check that IDs are not negative or zero

**"Missing permissions" errors:**
- Verify your bot has the required permissions
- Check the bot's role hierarchy in the guild

**"Rate limited" errors:**
- Laracord handles this automatically, but check your usage patterns
- Consider implementing additional rate limiting for high-traffic applications

### Debugging

Enable debug logging to troubleshoot request issues:

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

1. [Learn about error handling](./error-handling.md)
2. [Explore the API reference](../api.md)
3. [Understand authentication](./authentication.md) 
