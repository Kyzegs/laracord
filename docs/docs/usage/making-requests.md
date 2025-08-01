# Making API Requests

This guide covers how to make API requests using Laracord and handle responses effectively.

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

// Get guild information
$guild = Laracord::getGuild(987654321);
```

## Request Types

### GET Requests

For retrieving data from Discord:

```php
// Get current user
$user = Laracord::getCurrentUser();

// Get a specific channel
$channel = Laracord::getChannel($channelId);

// Get guild members
$members = Laracord::listGuildMembers($guildId);
```

### POST Requests

For creating new resources:

```php
// Create a message
$message = Laracord::createMessage($channelId, [
    'content' => 'Hello, world!',
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

// Modify a guild member
$updatedMember = Laracord::modifyGuildMember($guildId, $userId, [
    'nick' => 'New Nickname'
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

## Request Parameters

### Required Parameters

Most methods require specific parameters:

```php
// Channel operations require channel ID
$channel = Laracord::getChannel(123456789);

// Guild operations require guild ID
$guild = Laracord::getGuild(987654321);

// User operations require user ID
$user = Laracord::getUser(456789123);
```

### Optional Parameters

Many methods accept optional parameters:

```php
// Get guild with optional query parameters
$guild = Laracord::getGuild($guildId, [
    'with_counts' => true
]);

// List guild members with pagination
$members = Laracord::listGuildMembers($guildId, [
    'limit' => 100,
    'after' => $lastMemberId
]);
```

### Data Arrays

For creating or updating resources, pass data as arrays:

```php
// Create a message with rich content
$message = Laracord::createMessage($channelId, [
    'content' => 'Hello, Discord!',
    'tts' => false,
    'embeds' => [
        [
            'title' => 'Welcome!',
            'description' => 'This is a welcome message',
            'color' => 0x5865f2
        ]
    ],
    'components' => [
        [
            'type' => 1, // Action Row
            'components' => [
                [
                    'type' => 2, // Button
                    'style' => 1, // Primary
                    'label' => 'Click me!',
                    'custom_id' => 'my_button'
                ]
            ]
        ]
    ]
]);
```

## Response Handling

### Successful Responses

All methods return arrays containing the Discord API response:

```php
$channel = Laracord::getChannel($channelId);

// Access response data
echo $channel['name']; // Channel name
echo $channel['id'];   // Channel ID
echo $channel['type']; // Channel type
```

### Error Handling

Laracord throws exceptions for API errors:

```php
try {
    $channel = Laracord::getChannel($channelId);
} catch (HttpException $e) {
    switch ($e->getStatusCode()) {
        case 404:
            echo "Channel not found";
            break;
        case 403:
            echo "Bot lacks permissions";
            break;
        case 401:
            echo "Invalid token";
            break;
        default:
            echo "API error: " . $e->getMessage();
    }
}
```

## Rate Limiting

Laracord handles rate limiting automatically:

```php
// Multiple requests - rate limiting is handled automatically
for ($i = 0; $i < 10; $i++) {
    $message = Laracord::createMessage($channelId, [
        'content' => "Message $i"
    ]);
    // Laracord will automatically handle rate limits
}
```

## Best Practices

### Batch Operations

For multiple operations, consider batching:

```php
// Instead of multiple individual requests
$messages = [];
for ($i = 0; $i < 5; $i++) {
    $messages[] = Laracord::createMessage($channelId, [
        'content' => "Message $i"
    ]);
}

// Consider using bulk operations when available
// (Note: Discord doesn't have bulk message creation, but other APIs might)
```

### Error Recovery

Implement retry logic for transient errors:

```php
function makeRequestWithRetry($callback, $maxRetries = 3) {
    for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
        try {
            return $callback();
        } catch (HttpException $e) {
            if ($e->getStatusCode() >= 500 && $attempt < $maxRetries) {
                sleep($attempt * 2); // Exponential backoff
                continue;
            }
            throw $e;
        }
    }
}

// Usage
$channel = makeRequestWithRetry(function() use ($channelId) {
    return Laracord::getChannel($channelId);
});
```

### Logging

Log important operations for debugging:

```php
use Illuminate\Support\Facades\Log;

try {
    $message = Laracord::createMessage($channelId, $data);
    Log::info('Message created', [
        'channel_id' => $channelId,
        'message_id' => $message['id']
    ]);
} catch (Exception $e) {
    Log::error('Failed to create message', [
        'channel_id' => $channelId,
        'error' => $e->getMessage()
    ]);
    throw $e;
}
```

## Advanced Usage

### Custom Headers

For advanced use cases, you can customize the HTTP client:

```php
use GuzzleHttp\Client;
use Kyzegs\Laracord\Client as LaracordClient;

$guzzleClient = new Client([
    'headers' => [
        'User-Agent' => 'MyApp/1.0',
        'X-Custom-Header' => 'value'
    ]
]);

$laracordClient = new LaracordClient($guzzleClient);
```

### Request Timeouts

Configure timeouts for different operations:

```php
// In your config/laracord.php
return [
    'timeout' => 30, // 30 seconds default
    'retry_attempts' => 3,
    'retry_delay' => 1,
];
```

## Next Steps

1. [Learn about error handling](./error-handling.md)
2. [Explore the API reference](../api.md)
3. [Understand authentication](./authentication.md) 
