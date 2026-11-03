<?php

declare(strict_types=1);

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Kyzegs\Laracord\ClientFactory;
use Kyzegs\Laracord\DiscordClient;
use Kyzegs\Laracord\Facades\Laracord;
use Kyzegs\Laracord\Tests\TestCase;
use Kyzegs\Laracord\ValueObjects\Authentication;

uses(TestCase::class);

/** @param list<mixed> $pages */
function pagesOf(array $pages): MockHandler
{
    return new MockHandler(array_map(
        static fn (mixed $page): Response => new Response(200, [], (string) json_encode($page)),
        $pages,
    ));
}

function clientOnPages(MockHandler $pages): DiscordClient
{
    return resolve(ClientFactory::class)->make(Authentication::bot('secret'), HandlerStack::create($pages));
}

it('walks entities with a top-level id', function (): void {
    $pages = pagesOf([[['id' => '1'], ['id' => '2']], [['id' => '3']]]);

    $ids = clientOnPages($pages)->resource('entitlements')
        ->paginate('list', ['application_id' => '9'], perPage: 2)
        ->pluck('id')
        ->all();

    expect($ids)->toBe(['1', '2', '3'])
        ->and($pages->count())->toBe(0)
        ->and($pages->getLastRequest()?->getUri()->getQuery())->toContain('limit=2')->toContain('after=2');
});

it('walks every page of guild members and bans, which only carry user.id', function (string $endpoint): void {
    $pages = pagesOf([
        [['user' => ['id' => '1']], ['user' => ['id' => '2']]],
        [['user' => ['id' => '3']]],
    ]);

    $ids = clientOnPages($pages)->resource('guilds')
        ->paginate($endpoint, ['guild_id' => '9'], perPage: 2)
        ->pluck('user.id')
        ->all();

    expect($ids)->toBe(['1', '2', '3'])
        ->and($pages->getLastRequest()?->getUri()->getQuery())->toContain('after=2');
})->with(['listMembers', 'listBans']);

it('keeps every page when the results are collected', function (): void {
    $pages = pagesOf([[['id' => '1'], ['id' => '2']], [['id' => '3'], ['id' => '4']], [['id' => '5']]]);

    $items = clientOnPages($pages)->resource('entitlements')
        ->paginate('list', ['application_id' => '9'], perPage: 2)
        ->all();

    expect(array_column($items, 'id'))->toBe(['1', '2', '3', '4', '5']);
});

it('walks feeds that page by another field', function (): void {
    $pages = pagesOf([
        ['items' => [['pinned_at' => '2026-02-01T00:00:00+00:00', 'message' => ['id' => '1']]], 'has_more' => true],
        ['items' => [], 'has_more' => false],
    ]);

    $pins = clientOnPages($pages)->resource('channels')
        ->paginate('listPinnedMessages', ['channel_id' => '9'], cursor: 'before', perPage: 1, itemsKey: 'items', cursorKey: 'pinned_at')
        ->all();

    expect($pins)->toHaveCount(1)
        ->and(urldecode((string) $pages->getLastRequest()?->getUri()->getQuery()))->toContain('before=2026-02-01T00:00:00+00:00');
});

it('stops after the first short page', function (): void {
    $pages = pagesOf([[['id' => '1']], [['id' => 'never requested']]]);

    $ids = clientOnPages($pages)->resource('entitlements')
        ->paginate('list', ['application_id' => '9'], perPage: 100)
        ->pluck('id')
        ->all();

    expect($ids)->toBe(['1'])
        ->and($pages->count())->toBe(1);
});

it('stops when the cursor does not advance', function (): void {
    $fake = Laracord::fake([
        'guilds.listMembers' => Laracord::response([['user' => ['id' => '1']], ['user' => ['id' => '2']]]),
    ]);

    $members = Laracord::bot()->guilds()->paginate('listMembers', ['guild_id' => '9'], perPage: 2)->all();

    expect($members)->toHaveCount(4);
    $fake->assertSentCount(2);
});
