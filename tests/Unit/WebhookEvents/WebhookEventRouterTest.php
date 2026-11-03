<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Kyzegs\Laracord\Tests\TestCase;
use Kyzegs\Laracord\WebhookEvents\WebhookEvent;
use Kyzegs\Laracord\WebhookEvents\WebhookEventRouter;

uses(TestCase::class);

/** @param array<string, mixed>|string $payload */
function webhookRequest(array|string $payload): Request
{
    return Request::create(
        '/discord/events',
        'POST',
        server: ['CONTENT_TYPE' => 'application/json'],
        content: is_string($payload) ? $payload : (string) json_encode($payload),
    );
}

it('acknowledges pings without dispatching an event', function (): void {
    Event::fake([WebhookEvent::class]);

    $response = resolve(WebhookEventRouter::class)->handle(webhookRequest([
        'version' => 1,
        'application_id' => '123',
        'type' => 0,
    ]));

    expect($response->getStatusCode())->toBe(204)
        ->and($response->getContent())->toBe('');
    Event::assertNotDispatched(WebhookEvent::class);
});

it('dispatches webhook events and exposes their envelope', function (): void {
    Event::fake([WebhookEvent::class]);

    $response = resolve(WebhookEventRouter::class)->handle(webhookRequest([
        'version' => 1,
        'application_id' => '123',
        'type' => 1,
        'event' => [
            'type' => 'ENTITLEMENT_CREATE',
            'timestamp' => '2026-07-20T12:00:00Z',
            'data' => ['sku_id' => '456'],
        ],
    ]));

    expect($response->getStatusCode())->toBe(204);
    Event::assertDispatched(WebhookEvent::class, fn (WebhookEvent $event): bool => $event->version() === 1
        && $event->applicationId() === '123'
        && $event->eventType() === 'ENTITLEMENT_CREATE'
        && $event->timestamp() === '2026-07-20T12:00:00Z'
        && $event->data('sku_id') === '456');
});

it('rejects malformed webhook event payloads', function (array|string $payload): void {
    Event::fake([WebhookEvent::class]);

    $response = resolve(WebhookEventRouter::class)->handle(webhookRequest($payload));

    expect($response->getStatusCode())->toBe(400)
        ->and(json_decode((string) $response->getContent(), true))->toBe([
            'message' => 'Invalid Discord webhook event payload.',
        ]);
    Event::assertNotDispatched(WebhookEvent::class);
})->with([
    'invalid json' => ['{'],
    'unknown envelope type' => [[
        'version' => 1,
        'application_id' => '123',
        'type' => 2,
    ]],
    'missing event body' => [[
        'version' => 1,
        'application_id' => '123',
        'type' => 1,
    ]],
    'invalid event data' => [[
        'version' => 1,
        'application_id' => '123',
        'type' => 1,
        'event' => [
            'type' => 'ENTITLEMENT_CREATE',
            'timestamp' => '2026-07-20T12:00:00Z',
            'data' => null,
        ],
    ]],
]);
