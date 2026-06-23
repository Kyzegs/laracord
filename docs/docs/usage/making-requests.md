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

Dynamic endpoint methods use the same order without the `endpoint` argument. Because these calls are handled dynamically, pass their arguments positionally:

```php
$response = $discord->guilds()->get(
    ['guild_id' => $guildId],
    null,
    ['with_counts' => true],
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

$response = $discord->guilds()->modifyGuildMember(
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

## Uncatalogued endpoints

For a Discord route that has not yet reached the catalog, construct a `DiscordRequest` and send it directly:

```php
use Kyzegs\Laracord\Enums\HttpMethod;
use Kyzegs\Laracord\Http\DiscordRequest;

$response = $discord->send(new DiscordRequest(
    method: HttpMethod::Get,
    path: '/new-resource/{resource_id}',
    parameters: ['resource_id' => $resourceId],
));
```

## Next steps

1. [Handle errors](./error-handling.md)
2. [Understand rate limits](./rate-limits.md)
3. [Browse the endpoint catalog](../api/endpoints.md)
