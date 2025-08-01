# Error Handling

This guide covers how to handle errors and exceptions when using Laracord.

## Exception Types

Laracord throws different types of exceptions depending on the error:

### HttpException

Thrown for HTTP-level errors (4xx, 5xx status codes):

```php
use Symfony\Component\HttpKernel\Exception\HttpException;

try {
    $channel = Laracord::getChannel($channelId);
} catch (HttpException $e) {
    echo "HTTP Error: " . $e->getStatusCode();
    echo "Message: " . $e->getMessage();
}
```

### Common HTTP Status Codes

| Status | Meaning | Common Causes |
|--------|---------|---------------|
| 400 | Bad Request | Invalid parameters or malformed request |
| 401 | Unauthorized | Invalid or missing bot token |
| 403 | Forbidden | Bot lacks required permissions |
| 404 | Not Found | Resource doesn't exist |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Discord server error |
| 502 | Bad Gateway | Discord server temporarily unavailable |

## Error Handling Patterns

### Basic Try-Catch

```php
use Kyzegs\Laracord\Facades\Laracord;
use Symfony\Component\HttpKernel\Exception\HttpException;

try {
    $channel = Laracord::getChannel($channelId);
    echo "Channel: " . $channel['name'];
} catch (HttpException $e) {
    echo "Error: " . $e->getMessage();
}
```

### Status Code Specific Handling

```php
try {
    $message = Laracord::createMessage($channelId, $data);
} catch (HttpException $e) {
    switch ($e->getStatusCode()) {
        case 400:
            echo "Invalid request data";
            break;
        case 401:
            echo "Authentication failed - check your bot token";
            break;
        case 403:
            echo "Bot lacks required permissions";
            break;
        case 404:
            echo "Channel not found";
            break;
        case 429:
            echo "Rate limit exceeded - try again later";
            break;
        case 500:
        case 502:
        case 503:
            echo "Discord server error - try again later";
            break;
        default:
            echo "Unexpected error: " . $e->getMessage();
    }
}
```

### Retry Logic

For transient errors (5xx status codes), implement retry logic:

```php
function makeRequestWithRetry($callback, $maxRetries = 3) {
    for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
        try {
            return $callback();
        } catch (HttpException $e) {
            $statusCode = $e->getStatusCode();
            
            // Only retry on server errors
            if ($statusCode >= 500 && $attempt < $maxRetries) {
                $delay = $attempt * 2; // Exponential backoff
                sleep($delay);
                continue;
            }
            
            // Don't retry on client errors
            throw $e;
        }
    }
    
    throw new Exception("Max retries exceeded");
}

// Usage
$channel = makeRequestWithRetry(function() use ($channelId) {
    return Laracord::getChannel($channelId);
});
```

## Rate Limiting

### Automatic Rate Limiting

Laracord handles rate limiting automatically, but you can still encounter 429 errors:

```php
try {
    // Laracord automatically handles rate limits
    for ($i = 0; $i < 10; $i++) {
        $message = Laracord::createMessage($channelId, [
            'content' => "Message $i"
        ]);
    }
} catch (HttpException $e) {
    if ($e->getStatusCode() === 429) {
        // Rate limit exceeded despite automatic handling
        echo "Rate limit exceeded - wait before making more requests";
    }
}
```

### Manual Rate Limiting

For high-traffic applications, implement additional rate limiting:

```php
class RateLimiter {
    private array $requestCounts = [];
    private int $maxRequests = 50;
    private int $windowSeconds = 60;
    
    public function canMakeRequest(string $endpoint): bool {
        $now = time();
        $window = floor($now / $this->windowSeconds);
        $key = $endpoint . ':' . $window;
        
        if (!isset($this->requestCounts[$key])) {
            $this->requestCounts[$key] = 0;
        }
        
        if ($this->requestCounts[$key] >= $this->maxRequests) {
            return false;
        }
        
        $this->requestCounts[$key]++;
        return true;
    }
}

$rateLimiter = new RateLimiter();

if ($rateLimiter->canMakeRequest('createMessage')) {
    $message = Laracord::createMessage($channelId, $data);
} else {
    echo "Rate limit reached - wait before making more requests";
}
```

## Logging Errors

### Basic Logging

```php
use Illuminate\Support\Facades\Log;

try {
    $message = Laracord::createMessage($channelId, $data);
    Log::info('Message created successfully', [
        'channel_id' => $channelId,
        'message_id' => $message['id']
    ]);
} catch (HttpException $e) {
    Log::error('Failed to create message', [
        'channel_id' => $channelId,
        'status_code' => $e->getStatusCode(),
        'error' => $e->getMessage(),
        'data' => $data
    ]);
    throw $e;
}
```

### Structured Logging

```php
class DiscordLogger {
    public static function logRequest(string $method, array $params, $result = null, Exception $e = null) {
        $logData = [
            'method' => $method,
            'params' => $params,
            'timestamp' => now()->toISOString(),
        ];
        
        if ($result) {
            $logData['result'] = $result;
            Log::info('Discord API request successful', $logData);
        } else {
            $logData['error'] = $e->getMessage();
            $logData['status_code'] = $e instanceof HttpException ? $e->getStatusCode() : null;
            Log::error('Discord API request failed', $logData);
        }
    }
}

// Usage
try {
    $result = Laracord::getChannel($channelId);
    DiscordLogger::logRequest('getChannel', ['channelId' => $channelId], $result);
} catch (Exception $e) {
    DiscordLogger::logRequest('getChannel', ['channelId' => $channelId], null, $e);
    throw $e;
}
```

## Validation

### Input Validation

Validate inputs before making API calls:

```php
function validateChannelId($channelId): bool {
    return is_numeric($channelId) && $channelId > 0;
}

function validateMessageData(array $data): array {
    $errors = [];
    
    if (empty($data['content']) && empty($data['embeds'])) {
        $errors[] = 'Message must have content or embeds';
    }
    
    if (isset($data['content']) && strlen($data['content']) > 2000) {
        $errors[] = 'Message content too long (max 2000 characters)';
    }
    
    if (!empty($errors)) {
        throw new InvalidArgumentException(implode(', ', $errors));
    }
    
    return $data;
}

// Usage
try {
    $validatedData = validateMessageData($messageData);
    $message = Laracord::createMessage($channelId, $validatedData);
} catch (InvalidArgumentException $e) {
    echo "Validation error: " . $e->getMessage();
} catch (HttpException $e) {
    echo "API error: " . $e->getMessage();
}
```

## Error Recovery Strategies

### Graceful Degradation

```php
function getChannelWithFallback($channelId) {
    try {
        return Laracord::getChannel($channelId);
    } catch (HttpException $e) {
        if ($e->getStatusCode() === 404) {
            // Channel doesn't exist, return null instead of throwing
            return null;
        }
        throw $e;
    }
}

$channel = getChannelWithFallback($channelId);
if ($channel) {
    echo "Channel: " . $channel['name'];
} else {
    echo "Channel not found";
}
```

### Circuit Breaker Pattern

```php
class CircuitBreaker {
    private int $failureThreshold = 5;
    private int $timeout = 60;
    private int $failureCount = 0;
    private ?int $lastFailureTime = null;
    
    public function canExecute(): bool {
        if ($this->lastFailureTime && (time() - $this->lastFailureTime) < $this->timeout) {
            return $this->failureCount < $this->failureThreshold;
        }
        
        // Reset if timeout has passed
        $this->failureCount = 0;
        $this->lastFailureTime = null;
        return true;
    }
    
    public function recordSuccess(): void {
        $this->failureCount = 0;
        $this->lastFailureTime = null;
    }
    
    public function recordFailure(): void {
        $this->failureCount++;
        $this->lastFailureTime = time();
    }
}

$circuitBreaker = new CircuitBreaker();

if ($circuitBreaker->canExecute()) {
    try {
        $result = Laracord::getChannel($channelId);
        $circuitBreaker->recordSuccess();
    } catch (HttpException $e) {
        $circuitBreaker->recordFailure();
        throw $e;
    }
} else {
    echo "Circuit breaker open - skipping request";
}
```

## Testing Error Scenarios

### Mocking Errors

```php
// In your tests
public function test_handles_404_error() {
    $this->expectException(HttpException::class);
    $this->expectExceptionMessage('Channel not found');
    
    // Mock the facade to throw a 404
    Laracord::shouldReceive('getChannel')
        ->once()
        ->andThrow(new HttpException(404, 'Channel not found'));
    
    Laracord::getChannel(123456789);
}
```

## Next Steps

1. [Learn about making API requests](./making-requests.md)
2. [Explore the API reference](../api.md)
3. [Understand authentication](./authentication.md) 
