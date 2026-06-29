# Testing

`Laracord::fake()` swaps the Discord client for a recorder that captures every request and answers with canned responses — no HTTP calls leave your test suite.

```php
use Kyzegs\Laracord\Facades\Laracord;
use Kyzegs\Laracord\Payloads\DiscordMessage;

$fake = Laracord::fake();

Laracord::bot()->messages()->create(
    ['channel_id' => '123'],
    (new DiscordMessage)->content('Hello'),
);

$fake->assertSent('messages', 'create');
```

## Stubbing responses

Pass an array keyed by `resource.endpoint` (the same names you call on the client). Values are built with `Laracord::response()`. Keys support wildcards: `resource.*` matches any endpoint on a resource, and `*` is the catch-all.

```php
Laracord::fake([
    'messages.create' => Laracord::response(['id' => '999'], status: 201),
    'guilds.*'        => Laracord::response(['id' => 'g1']),
    '*'               => Laracord::response('', status: 204),
]);

$id = Laracord::bot()->messages()->create(['channel_id' => '1'])->json('id'); // '999'
```

A stub value may also be:

- a **`Throwable`** — thrown when the endpoint is called (handy for exercising rate-limit or error paths);
- a **`Closure`** receiving the `DiscordRequest` and returning a response;
- a **list** of the above, consumed in order (the last entry repeats once exhausted).

Unmatched requests return an empty `200` by default.

## Assertions

`Laracord::fake()` returns the fake; assert against it:

```php
$fake = Laracord::fake();

// ... exercise your code ...

$fake->assertSent('messages', 'create', fn ($request) => $request->bodyArray()['content'] === 'Hello');
$fake->assertNotSent('messages', 'delete');
$fake->assertSentCount(1);
$fake->assertNothingSent();
```

The callback receives the recorded `DiscordRequest`, exposing `parameters`, `query`, `bodyArray()`, `files`, `resource`, and `endpoint`.
