<?php

declare(strict_types=1);

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Kyzegs\Laracord\ClientFactory;
use Kyzegs\Laracord\Tests\TestCase;
use Kyzegs\Laracord\ValueObjects\Authentication;

uses(TestCase::class);

it('lazily paginates following the cursor until a short page', function (): void {
    $history = [];
    $stack = HandlerStack::create(new MockHandler([
        new Response(200, [], (string) json_encode([['id' => '1'], ['id' => '2']])),
        new Response(200, [], (string) json_encode([['id' => '3']])),
    ]));
    $stack->push(Middleware::history($history));

    $client = resolve(ClientFactory::class)->make(Authentication::bot('secret'), $stack);

    $ids = $client->resource('guilds')
        ->paginate('listMembers', ['guild_id' => '9'], perPage: 2)
        ->pluck('id')
        ->all();

    expect($ids)->toBe(['1', '2', '3'])
        ->and($history)->toHaveCount(2)
        ->and($history[0]['request']->getUri()->getQuery())->toBe('limit=2')
        ->and($history[1]['request']->getUri()->getQuery())->toContain('after=2');
});

it('stops after the first short page', function (): void {
    $stack = HandlerStack::create(new MockHandler([
        new Response(200, [], (string) json_encode([['id' => '1']])),
    ]));
    $client = resolve(ClientFactory::class)->make(Authentication::bot('secret'), $stack);

    $ids = $client->resource('guilds')
        ->paginate('listMembers', ['guild_id' => '9'], perPage: 100)
        ->pluck('id')
        ->all();

    expect($ids)->toBe(['1']);
});
