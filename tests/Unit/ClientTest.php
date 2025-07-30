<?php

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Sleep;
use Kyzegs\Laracord\Client;
use Kyzegs\Laracord\Middleware\RatelimitMiddleware;
use Kyzegs\Laracord\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    config(['cache.default' => 'array']);
    Sleep::fake();
});

it('retries server errors', function () {
    $mock = new MockHandler([
        new Response(500, ['Content-Type' => 'application/json'], '{}'),
        new Response(200, ['Content-Type' => 'application/json'], json_encode(['ok' => true])),
    ]);

    $stack = HandlerStack::create($mock);
    $stack->push(new RatelimitMiddleware);

    $client = new Client(new GuzzleClient(['handler' => $stack]));

    $data = $client->getChannel(1);

    expect($data)->toBe(['ok' => true]);
    Sleep::assertSleptTimes(1);
});
