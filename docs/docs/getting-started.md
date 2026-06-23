# Getting Started

Laracord 1.0 wraps Discord's HTTP platform in immutable authentication contexts and resource clients.

## Requirements

- PHP 8.2+
- Laravel 12 or 13
- `ext-json` and `ext-sodium`

## Bot request

```php
use Kyzegs\Laracord\Facades\Laracord;

$response = Laracord::bot()->channels()->get([
    'channel_id' => '123456789012345678',
]);

$channel = $response->json();
```

## Resource calls

Every resource client accepts endpoint name, route parameters, body, query, files, and optional audit reason:

```php
$response = Laracord::bot()->messages()->call(
    'create',
    ['channel_id' => $channelId],
    ['content' => 'Hello'],
);
```

Responses preserve status, headers, raw body, decoded JSON, and rate-limit metadata. Empty `204` responses return normally.
