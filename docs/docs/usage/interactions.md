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

Then hand the request to the router from a controller behind the signature middleware:

```php
Route::post('/discord/interactions', function (Request $request, InteractionRouter $router) {
    return $router->handle($request);
})->middleware(VerifyDiscordSignature::class);
```

Handlers may be closures, invokable class names, `Class@method` strings, or `[Class::class, 'method']` pairs; they are resolved through the container. A handler receives the resolved `Interaction` (parameter named `$interaction`) and, for wildcard `custom_id` routes, the captured segments as `$parameters`. It must return a Symfony `Response` — build one with `InteractionResponse`.

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

The `Interaction` value object exposes `id()`, `type()`, `isPing()`, `token()`, `data()`, `commandName()`, `customId()`, `values()` (selected component values), and `option(name)` (a command option value by name). An unmatched interaction throws `UnhandledInteractionException`.
