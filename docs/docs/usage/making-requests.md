# Making API Requests

Laracord groups Discord endpoints into resource clients. Start with an authentication context, select a resource, and call an endpoint from the [endpoint catalog](../api/endpoints.md).

```php
use Kyzegs\Laracord\Facades\Laracord;

$discord = Laracord::bot();

$channel = $discord->channels()->get([
    'channel_id' => '123456789012345678',
]);

$message = $discord->messages()->create(
    ['channel_id' => '123456789012345678'],
    ['content' => 'Hello, Discord!'],
);
```

Endpoint methods belong on their resource client; they are not methods on the `Laracord` facade. Common resources offer concise names such as `get`, `create`, `edit`, and `delete`. The endpoint catalog also includes longer route-specific names where needed.

## Arguments

Every generated endpoint call accepts the same arguments, in this order:

```php
$response = $discord->resourceName()->call(
    endpoint: 'endpointName',
    parameters: [],
    body: null,
    query: [],
    files: [],
    auditLogReason: null,
);
```

Dynamic endpoint methods use the same order without the `endpoint` argument. Named arguments work too:

```php
$response = $discord->guilds()->get(
    ['guild_id' => $guildId],
    query: ['with_counts' => true],
);
```

### Route parameters

Use the exact placeholder names shown in the catalog. Snowflakes should be strings so PHP does not truncate them on unsupported integer platforms.

```php
$response = $discord->messages()->get([
    'channel_id' => $channelId,
    'message_id' => $messageId,
]);
```

### Request bodies

Pass an array, Laravel `Arrayable`, or `JsonSerializable` object as the second dynamic-method argument:

```php
$response = $discord->messages()->edit(
    [
        'channel_id' => $channelId,
        'message_id' => $messageId,
    ],
    ['content' => 'Updated content'],
);
```

### Query parameters

Query parameters are the third dynamic-method argument. Pass `null` for the body when an endpoint only needs a query:

```php
$response = $discord->guilds()->listMembers(
    ['guild_id' => $guildId],
    null,
    [
        'limit' => 100,
        'after' => $lastMemberId,
    ],
);
```

### Audit-log reasons

Use `AuditLogReason` for endpoints that support Discord's audit-log reason header:

```php
use Kyzegs\Laracord\ValueObjects\AuditLogReason;

$response = $discord->guilds()->editMember(
    [
        'guild_id' => $guildId,
        'user_id' => $userId,
    ],
    ['nick' => 'New nickname'],
    [],
    [],
    new AuditLogReason('Requested by support'),
);
```

## Responses

All requests return `DiscordResponse`:

```php
$response = $discord->channels()->get([
    'channel_id' => $channelId,
]);

echo $response->status();
echo $response->json('name');

$headers = $response->headers();
$rawBody = $response->body();
$rateLimit = $response->rateLimit();
```

For a `204 No Content` response, `isNoContent()` returns `true` and `json()` returns `null`:

```php
$response = $discord->messages()->delete([
    'channel_id' => $channelId,
    'message_id' => $messageId,
]);

if ($response->isNoContent()) {
    // Deleted successfully.
}
```

### Collections and fluent access

`collect()` wraps the decoded JSON (or a sub-key) in a `Collection`, and `fluent()` wraps an object body in a `Fluent`:

```php
$ids = $discord->guilds()->listMembers(['guild_id' => $guildId])
    ->collect()
    ->pluck('user.id');

$name = $discord->guilds()->get(['guild_id' => $guildId])
    ->fluent()
    ->get('name');
```

## Pagination

`paginate()` lazily walks a cursor-paginated endpoint, advancing by the last item on each page until a short page is returned. It returns a `LazyCollection`, so only one page is held in memory at a time:

```php
$discord->guilds()
    ->paginate('listMembers', ['guild_id' => $guildId], perPage: 1000)
    ->each(function (array $member): void {
        // ...
    });
```

Defaults suit ascending-id collections, including guild members and bans, whose cursor is `user.id`. For reverse-chronological history pass the matching cursor, for nested payloads pass the items key, and for feeds that page by another field pass `cursorKey`:

```php
$discord->messages()->paginate('list', ['channel_id' => $channelId], cursor: 'before');

$discord->auditLogs()->paginate('get', ['guild_id' => $guildId], cursor: 'before', itemsKey: 'audit_log_entries');

$discord->channels()->paginate('listPinnedMessages', ['channel_id' => $channelId], cursor: 'before', perPage: 50, itemsKey: 'items', cursorKey: 'pinned_at');
```

## Concurrent requests

`pool()` dispatches several requests at once and returns the results keyed exactly as you built them. Each value is a `DiscordResponse`, or the `Throwable` that request failed with. Pooled requests skip the server-error retry loop.

```php
use Kyzegs\Laracord\Pool\Pool;

$results = Laracord::bot()->pool(fn (Pool $pool) => [
    'general' => $pool->request('channels', 'get', ['channel_id' => '1']),
    'random' => $pool->request('channels', 'get', ['channel_id' => '2']),
]);

$results['general']->json('name');
```

## Multipart file uploads

Pass Guzzle-compatible multipart file definitions as the fourth dynamic-method argument:

```php
$stream = fopen(storage_path('app/report.pdf'), 'rb');

$response = $discord->messages()->create(
    ['channel_id' => $channelId],
    [
        'content' => 'Monthly report',
        'attachments' => [
            ['id' => 0, 'filename' => 'report.pdf'],
        ],
    ],
    [],
    [[
        'name' => 'files[0]',
        'contents' => $stream,
        'filename' => 'report.pdf',
        'content_type' => 'application/pdf',
    ]],
);
```

## Message builder

`DiscordMessage` works anywhere a message body does, and its files are uploaded without passing them separately. It defaults to no parsed mentions:

```php
use Kyzegs\Laracord\Constants\MessageFlags;
use Kyzegs\Laracord\Payloads\DiscordMessage;

$discord->messages()->create(['channel_id' => $channelId], (new DiscordMessage)
    ->content('Monthly report')
    ->reply($messageId)
    ->file($stream, 'report.pdf', 'application/pdf', description: 'October figures', spoiler: true)
    ->flags(MessageFlags::SUPPRESS_NOTIFICATIONS));
```

`forward($messageId, $channelId)`, `stickers()`, `poll()`, and `sharedClientTheme()` cover the other message features. `threadName()` and `appliedTags()` apply when a webhook posts to a forum or media channel.

Laracord does not repeat a `POST` that timed out or got a `5xx`, because it may have taken effect. To retry `messages()->create()` yourself without posting twice, set `nonce()` and send the same value again: Discord returns the original message instead of a duplicate. Webhooks and interaction follow-ups do not take a nonce, so retrying those can post twice.

## Endpoint notes

Discord changes how some endpoints behave without changing their routes:

- `guilds()->listChannels()` omits channels your bot cannot view from **November 16, 2026**. Bots that audit or mirror a whole server's channel list need `VIEW_CHANNEL` on those channels.
- `users()->listGuilds()` returns `400` for apps using large bot sharding unless you pass the `shard` query parameter.
- `invites()->editTargetUsers()` replaces the whole list from an uploaded CSV as a background job. To change a few users, use `addTargetUser()`, `removeTargetUser()`, `bulkAddTargetUsers()`, or `bulkRemoveTargetUsers()`.

## Uncatalogued endpoints

For a Discord route that has not yet reached the catalog, construct a `DiscordRequest` and send it directly:

```php
use Kyzegs\Laracord\Enums\HttpMethod;
use Kyzegs\Laracord\Http\DiscordRequest;

$response = $discord->send(new DiscordRequest(
    method: HttpMethod::GET,
    path: '/new-resource/{resource_id}',
    parameters: ['resource_id' => $resourceId],
));
```

## Next steps

1. [Handle errors](./error-handling.md)
2. [Understand rate limits](./rate-limits.md)
3. [Browse the endpoint catalog](../api/endpoints.md)
