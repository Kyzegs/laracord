<?php

declare(strict_types=1);

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;
use Kyzegs\Laracord\ClientFactory;
use Kyzegs\Laracord\DiscordClient;
use Kyzegs\Laracord\Events\RequestFailed;
use Kyzegs\Laracord\Events\RequestSending;
use Kyzegs\Laracord\Events\ResponseReceived;
use Kyzegs\Laracord\Tests\TestCase;
use Kyzegs\Laracord\ValueObjects\Authentication;

uses(TestCase::class);

function clientWith(MockHandler $handler): DiscordClient
{
    return resolve(ClientFactory::class)->make(Authentication::bot('secret'), HandlerStack::create($handler));
}

it('dispatches sending and received events on success', function (): void {
    Event::fake();

    clientWith(new MockHandler([new Response(200, [], '{"ok":true}')]))
        ->resource('users')->call('getCurrent');

    Event::assertDispatched(RequestSending::class);
    Event::assertDispatched(ResponseReceived::class);
    Event::assertNotDispatched(RequestFailed::class);

    $sending = Event::dispatched(RequestSending::class)->first()[0];
    $received = Event::dispatched(ResponseReceived::class)->first()[0];

    expect($sending->requestId)->not->toBeEmpty()
        ->and($received->requestId)->toBe($sending->requestId)
        ->and($received->durationMs)->toBeGreaterThanOrEqual(0)
        ->and($received->attempts)->toBe(1);
});

it('dispatches a failed event on an http error', function (): void {
    Event::fake();

    try {
        clientWith(new MockHandler([new Response(404, [], '{}')]))
            ->resource('users')->call('getCurrent');
    } catch (Throwable) {
        // mapped to a DiscordNotFoundException; assertion is on the event
    }

    Event::assertDispatched(RequestFailed::class);
    Event::assertNotDispatched(ResponseReceived::class);
});

it('reports server retry attempts', function (): void {
    Event::fake();
    Sleep::fake();
    config()->set('laracord.http.server_retries', 2);

    clientWith(new MockHandler([new Response(500), new Response(200, [], '{}')]))
        ->resource('users')->call('getCurrent');

    $received = Event::dispatched(ResponseReceived::class)->first()[0];

    expect($received->attempts)->toBe(2);
});
