<?php

declare(strict_types=1);

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Kyzegs\Laracord\ClientFactory;
use Kyzegs\Laracord\Exceptions\DiscordNotFoundException;
use Kyzegs\Laracord\Facades\Laracord;
use Kyzegs\Laracord\Http\DiscordResponse;
use Kyzegs\Laracord\Pool\Pool;
use Kyzegs\Laracord\Tests\TestCase;
use Kyzegs\Laracord\ValueObjects\Authentication;

uses(TestCase::class);

it('sends pooled requests and preserves keys', function (): void {
    $stack = HandlerStack::create(new MockHandler([
        new Response(200, [], '{"id":"1"}'),
        new Response(200, [], '{"id":"2"}'),
    ]));
    $client = resolve(ClientFactory::class)->make(Authentication::bot('secret'), $stack);

    $results = $client->pool(fn (Pool $pool): array => [
        'a' => $pool->request('channels', 'get', ['channel_id' => '1']),
        'b' => $pool->request('channels', 'get', ['channel_id' => '2']),
    ]);

    $a = $results['a'];
    $b = $results['b'];
    expect($a)->toBeInstanceOf(DiscordResponse::class)
        ->and($b)->toBeInstanceOf(DiscordResponse::class);
    assert($a instanceof DiscordResponse && $b instanceof DiscordResponse);
    expect($a->json('id'))->toBe('1')
        ->and($b->json('id'))->toBe('2');
});

it('collects pooled failures as exceptions', function (): void {
    $stack = HandlerStack::create(new MockHandler([
        new Response(200, [], '{"id":"1"}'),
        new Response(404, [], '{}'),
    ]));
    $client = resolve(ClientFactory::class)->make(Authentication::bot('secret'), $stack);

    $results = $client->pool(fn (Pool $pool): array => [
        $pool->request('channels', 'get', ['channel_id' => '1']),
        $pool->request('channels', 'get', ['channel_id' => '2']),
    ]);

    expect($results[0])->toBeInstanceOf(DiscordResponse::class)
        ->and($results[1])->toBeInstanceOf(DiscordNotFoundException::class);
});

it('records pooled requests through the fake', function (): void {
    $fake = Laracord::fake([
        'channels.get' => Laracord::response(['id' => 'faked']),
    ]);

    $results = Laracord::bot()->pool(fn (Pool $pool): array => [
        $pool->request('channels', 'get', ['channel_id' => '1']),
        $pool->request('channels', 'get', ['channel_id' => '2']),
    ]);

    $first = $results[0];
    assert($first instanceof DiscordResponse);
    expect($first->json('id'))->toBe('faked');
    $fake->assertSentCount(2);
});
