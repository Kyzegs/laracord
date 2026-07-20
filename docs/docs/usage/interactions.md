# Interactions and Webhook Events

Attach `VerifyDiscordSignature` to application-owned routes. It validates Ed25519 signature, raw body, timestamp, and replay window.

```php
Route::post('/discord/interactions', function (Request $request) {
    $interaction = new Interaction($request->json()->all());

    return $interaction->type() === 1
        ? InteractionResponse::pong()
        : InteractionResponse::defer();
})->middleware(VerifyDiscordSignature::class);
```

Use `InteractionResponse::webhookPong()` for webhook-event PING payloads. Laracord does not register routes automatically.

## Routing interactions

`InteractionRouter` maps incoming interactions to handlers so you don't hand-write a `type`/`custom_id` switch. It auto-answers `PING`, dispatches application commands and autocomplete by name, and message components and modal submits by `custom_id`. Register routes once (e.g. in a service provider) on the shared singleton via the `Laracord` facade:

```php
use Kyzegs\Laracord\Facades\Laracord;

Laracord::interactions()
    ->command('ban', BanCommand::class)             // type 2 — by command name
    ->autocomplete('search', SearchHandler::class)  // type 4 — by command name
    ->component('vote:*', VoteController::class)     // type 3 — wildcard custom_id
    ->modal('feedback', FeedbackHandler::class);     // type 5 — by custom_id
```

Handlers that only dispatch a queued job can be acknowledged automatically. Commands and modals use a deferred channel message (`type: 5`); components use a deferred update (`type: 6`). The handler runs first so a failed job dispatch is not acknowledged accidentally.

```php
Laracord::interactions()->command(
    'report',
    fn (Interaction $interaction) => BuildReport::dispatch($interaction->payload),
    defer: true,
    ephemeral: true,
);
```

Then hand the request to the router from a controller behind the signature middleware:

```php
Route::post('/discord/interactions', function (Request $request, InteractionRouter $router) {
    return $router->handle($request);
})->middleware(VerifyDiscordSignature::class);
```

Handlers may be closures, invokable class names, `Class@method` strings, or `[Class::class, 'method']` pairs; they are resolved through the container. A handler may receive the resolved `Interaction` as `$interaction`, its `InteractionContext` as `$context`, and wildcard `custom_id` captures as `$parameters`. A normal handler must return a Symfony `Response` — build one with `InteractionResponse`. A deferred handler may return anything because the router returns the acknowledgement.

```php
final class VoteController
{
    public function __invoke(Interaction $interaction, array $parameters): JsonResponse
    {
        [$pollId] = $parameters; // captured from 'vote:*'

        return InteractionResponse::message(
            (new DiscordMessage)->content("Recorded vote for poll {$pollId}."),
        );
    }
}
```

## Response lifecycle

Use the context after the initial response to retrieve, edit, or delete the original message and to manage follow-ups. These requests use the interaction token and do not send bot authentication.

```php
final class ReportHandler
{
    public function __invoke(InteractionContext $context): JsonResponse
    {
        BuildReport::dispatch($context->interaction->payload);

        return $context->defer(ephemeral: true);
    }
}

// Later, while the interaction token is valid:
$context->getOriginal();
$context->editOriginal((new DiscordMessage)->content('Report ready.'));
$context->deleteOriginal();

$message = $context->followup((new DiscordMessage)->content('More detail.'));
$context->getFollowup($message->json('id'));
$context->editFollowup($message->json('id'), ['content' => 'Updated detail.']);
$context->deleteFollowup($message->json('id'));
```

Discord interaction tokens expire 15 minutes after the timestamp encoded in the interaction snowflake. `createdAt()`, `expiresAt()`, and `isExpired()` expose that lifetime; context HTTP methods reject expired tokens before sending a request. Initial responses must still be sent within Discord's three-second deadline.

## Payload accessors

`option()` accepts a dot-delimited path through subcommand groups and subcommands:

```php
$userId = $interaction->option('admin.ban.user');
$option = $interaction->optionData('admin.ban.user');
```

Resolved Discord objects are available through `resolvedUsers()`, `resolvedMembers()`, `resolvedRoles()`, `resolvedChannels()`, `resolvedMessages()`, and `resolvedAttachments()`. Use `resolved('users', $userId)` to retrieve one object. The original accessors — `id()`, `applicationId()`, `type()`, `isPing()`, `token()`, `data()`, `commandName()`, `customId()`, `values()`, and top-level `option(name)` — remain available.

See Discord's [Receiving and Responding to Interactions](https://docs.discord.com/developers/interactions/receiving-and-responding) reference for callback constraints and token lifetime. An unmatched interaction throws `UnhandledInteractionException`.
