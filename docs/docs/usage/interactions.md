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
