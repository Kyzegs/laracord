# Events

The client dispatches lifecycle events around every request, so you can add logging, metrics, or tracing without wrapping your own calls.

| Event | Dispatched | Payload |
| --- | --- | --- |
| `RequestSending` | before a request leaves the client | `$request`, `$requestId` |
| `ResponseReceived` | after a successful (2xx) response | `$request`, `$response`, `$requestId`, `$durationMs`, `$attempts` |
| `RequestFailed` | on a transport or HTTP error | `$request`, `$exception`, `$requestId`, `$durationMs`, `$attempts` |

All three live in `Kyzegs\Laracord\Events`. `$request` is a `DiscordRequest` (exposing `method`, `resource`, `endpoint`, `parameters`, `query`, ...) and `$response` is a `DiscordResponse`.

```php
use Illuminate\Support\Facades\Event;
use Kyzegs\Laracord\Events\RequestFailed;
use Kyzegs\Laracord\Events\ResponseReceived;

Event::listen(ResponseReceived::class, function (ResponseReceived $event): void {
    logger()->debug('Discord call', [
        'resource' => $event->request->resource,
        'endpoint' => $event->request->endpoint,
        'status' => $event->response->status(),
    ]);
});

Event::listen(RequestFailed::class, function (RequestFailed $event): void {
    report($event->exception);
});
```

Pooled requests dispatch the same events per request. They fire after the server-error retry loop, so `RequestFailed` reflects the final outcome rather than each retry.

## Telescope

When `laravel/telescope` is installed, Laracord automatically records completed Discord calls in Telescope's **Client Requests** screen. Entries include duration (including retry and rate-limit delays), server retry count, resource and endpoint names, response status, rate-limit headers, bucket ID, and a correlation ID shared by the lifecycle events.

Request and response bodies, authorization headers, query values, and resolved route parameters are never recorded. Route templates are used so webhook and interaction tokens cannot leak into Telescope.

Disable the integration with `LARACORD_TELESCOPE=false`. Telescope's own filters, tags, retention, and pruning configuration continue to apply.
