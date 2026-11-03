<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\WebhookEvents;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

final readonly class WebhookEventRouter
{
    public function __construct(private Dispatcher $events) {}

    public function handle(Request|WebhookEvent $event): Response
    {
        try {
            $event = $event instanceof WebhookEvent ? $event : WebhookEvent::from($event);
        } catch (InvalidArgumentException $invalidArgumentException) {
            return new JsonResponse(['message' => $invalidArgumentException->getMessage()], 400);
        }

        if (! $event->isPing()) {
            $this->events->dispatch($event);
        }

        return new Response('', 204);
    }
}
