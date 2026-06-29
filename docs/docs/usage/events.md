# Events

The client dispatches lifecycle events around every request, so you can add logging, metrics, or tracing without wrapping your own calls.

| Event | Dispatched | Payload |
| --- | --- | --- |
| `RequestSending` | before a request leaves the client | `$request` |
| `ResponseReceived` | after a successful (2xx) response | `$request`, `$response` |
| `RequestFailed` | on a transport or HTTP error | `$request`, `$exception` |

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
