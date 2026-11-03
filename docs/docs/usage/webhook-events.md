# Webhook Events

Discord sends Webhook Events as signed HTTP `POST` requests. Route them through `VerifyDiscordSignature`, then let `WebhookEventRouter` acknowledge PINGs and dispatch real events through Laravel:

```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Kyzegs\Laracord\Interactions\VerifyDiscordSignature;
use Kyzegs\Laracord\WebhookEvents\WebhookEventRouter;

Route::post('/discord/events', function (Request $request, WebhookEventRouter $router) {
    return $router->handle($request);
})->middleware(VerifyDiscordSignature::class);
```

The router returns an empty `204` for both endpoint-verification PINGs and accepted events. Invalid envelopes return `400`; invalid signatures are rejected by the middleware with `401`.

## Handling events

Listen for `WebhookEvent` with Laravel's event system. The router runs listeners before it responds, and Discord requires the `204` within three seconds. If your endpoint fails to respond too often, Discord stops sending events and notifies you by email, so use a queued listener for anything that may take time:

```php
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Event;
use Kyzegs\Laracord\WebhookEvents\WebhookEvent;

final class HandleDiscordWebhook implements ShouldQueue
{
    public function handle(WebhookEvent $event): void
    {
        if ($event->eventType() === 'ENTITLEMENT_CREATE') {
            $skuId = $event->data('sku_id');
            // Update the entitlement...
        }
    }
}

Event::listen(WebhookEvent::class, HandleDiscordWebhook::class);
```

`version()`, `applicationId()`, `eventType()`, `timestamp()`, and `data()` expose the documented envelope. The original forward-compatible payload remains available as `$event->payload`.

Webhook events are not guaranteed to arrive in real time or in order. Discord retries failed acknowledgements with exponential backoff for up to ten minutes. See Discord's [Webhook Events documentation](https://docs.discord.com/developers/events/webhook-events) for subscription and payload details.
