# Error Handling

Laracord throws package-specific exceptions for Discord HTTP failures, exhausted safety limits, and transport errors.

## HTTP exceptions

Every non-successful Discord response becomes a `DiscordHttpException`. Common statuses have more specific subclasses:

| Status | Exception |
|---:|---|
| `401` | `DiscordAuthenticationException` |
| `403` | `DiscordForbiddenException` |
| `404` | `DiscordNotFoundException` |
| `500`, `502`, `504`, `524` | `DiscordServerException` |
| Other non-2xx | `DiscordHttpException` |

The exception exposes both the `DiscordResponse` and the original `DiscordRequest`:

```php
use Kyzegs\Laracord\Exceptions\DiscordHttpException;
use Kyzegs\Laracord\Facades\Laracord;

try {
    $channel = Laracord::bot()->channels()->get([
        'channel_id' => $channelId,
    ]);
} catch (DiscordHttpException $exception) {
    report($exception);

    $status = $exception->response->status();
    $discordCode = $exception->response->json('code');
    $discordMessage = $exception->response->json('message');
}
```

Catch a subclass when your application can recover from that specific condition:

```php
use Kyzegs\Laracord\Exceptions\DiscordForbiddenException;
use Kyzegs\Laracord\Exceptions\DiscordNotFoundException;

try {
    return Laracord::bot()
        ->channels()
        ->get(['channel_id' => $channelId])
        ->json();
} catch (DiscordNotFoundException) {
    return null;
} catch (DiscordForbiddenException $exception) {
    report($exception);

    abort(403, 'The bot cannot access this channel.');
}
```

## Rate-limit and safety exceptions

The rate-limit middleware normally waits and retries automatically. It throws when a configured retry or delay boundary is exhausted:

```php
use Kyzegs\Laracord\Exceptions\DiscordInvalidRequestLimitException;
use Kyzegs\Laracord\Exceptions\DiscordRateLimitException;

try {
    $response = Laracord::bot()->messages()->create(
        ['channel_id' => $channelId],
        ['content' => 'Hello'],
    );
} catch (DiscordRateLimitException $exception) {
    logger()->warning('Discord rate limit exhausted', [
        'retry_after' => $exception->retryAfter,
        'global' => $exception->global,
    ]);
} catch (DiscordInvalidRequestLimitException $exception) {
    logger()->critical('Discord invalid-request budget exhausted', [
        'retry_after' => $exception->retryAfter,
    ]);
}
```

Do not add a second blind retry loop around all requests. Laracord already retries the Discord server statuses and connection failures configured by the client. If your application queues a retry after one of these exceptions, use the exposed `retryAfter` value.

## Transport and authentication errors

- `DiscordTransportException` indicates that connection retries were exhausted or no response was produced.
- `MissingAuthenticationException` means a protected endpoint was called through `withoutAuthentication()`.
- `DiscordException` is the common parent for all package exceptions.

```php
use Kyzegs\Laracord\Exceptions\DiscordException;
use Kyzegs\Laracord\Exceptions\DiscordTransportException;

try {
    $response = Laracord::bot()->users()->getCurrentUser();
} catch (DiscordTransportException $exception) {
    report($exception);

    return response()->json(['error' => 'Discord is unavailable'], 503);
} catch (DiscordException $exception) {
    report($exception);

    throw $exception;
}
```

## Logging safely

Log endpoint names, route parameters, status, and Discord's error code. Avoid logging authorization headers, OAuth tokens, webhook tokens, or complete request URLs containing secrets.

```php
try {
    $message = Laracord::bot()->messages()->create(
        ['channel_id' => $channelId],
        $data,
    );
} catch (DiscordHttpException $exception) {
    logger()->error('Discord message creation failed', [
        'channel_id' => $channelId,
        'status' => $exception->response->status(),
        'discord_code' => $exception->response->json('code'),
    ]);

    throw $exception;
}
```

## Next steps

1. [Make API requests](./making-requests.md)
2. [Understand rate limits](./rate-limits.md)
3. [Browse the API reference](../api.md)
