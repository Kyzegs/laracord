<?php

declare(strict_types=1);

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Sleep;
use Kyzegs\Laracord\Client;
use Kyzegs\Laracord\Middleware\RatelimitMiddleware;
use Kyzegs\Laracord\Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    config(['cache.default' => 'array']);
    Sleep::fake();
});

it('sleeps when bucket is exhausted', function (): void {
    $mock = new MockHandler([
        new Response(200, [
            'Content-Type' => 'application/json',
            'X-Ratelimit-Bucket' => 'abc',
            'X-Ratelimit-Limit' => '1',
            'X-Ratelimit-Remaining' => '0',
            'X-Ratelimit-Reset-After' => '1',
            'X-Ratelimit-Reset' => microtime(true) + 1,
        ], '{}'),
        new Response(200, ['Content-Type' => 'application/json'], '{}'),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $handlerStack->push(new RatelimitMiddleware);

    $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

    $client->getChannelMessages(1);
    $client->getChannelMessages(1);

    Sleep::assertSleptTimes(1);
});

it('retries after 429 response', function (): void {
    $mock = new MockHandler([
        new Response(429, [
            'Content-Type' => 'application/json',
            'X-Ratelimit-Bucket' => 'abc',
        ], json_encode(['retry_after' => 0.5])),
        new Response(200, ['Content-Type' => 'application/json', 'X-Ratelimit-Bucket' => 'abc'], '{}'),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $handlerStack->push(new RatelimitMiddleware);

    $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

    $client->getChannelMessages(1);

    Sleep::assertSleptTimes(1);
});
