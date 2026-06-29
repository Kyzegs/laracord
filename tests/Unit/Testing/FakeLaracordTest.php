<?php

declare(strict_types=1);

use Kyzegs\Laracord\Exceptions\DiscordRateLimitException;
use Kyzegs\Laracord\Facades\Laracord;
use Kyzegs\Laracord\Http\DiscordRequest;
use Kyzegs\Laracord\Http\DiscordResponse;
use Kyzegs\Laracord\Payloads\DiscordMessage;
use Kyzegs\Laracord\Tests\TestCase;

uses(TestCase::class);

it('records requests and asserts what was sent', function (): void {
    $fake = Laracord::fake();

    Laracord::bot()->messages()->create(
        ['channel_id' => '123'],
        (new DiscordMessage)->content('Hello'),
    );

    $fake->assertSent('messages', 'create');
    $fake->assertSent('messages', 'create', function (DiscordRequest $request): bool {
        $body = $request->bodyArray();

        return $request->parameters['channel_id'] === '123' && ($body['content'] ?? null) === 'Hello';
    });
    $fake->assertNotSent('messages', 'delete');
    $fake->assertSentCount(1);
});

it('returns a stubbed response keyed by resource.endpoint', function (): void {
    Laracord::fake([
        'messages.create' => Laracord::response(['id' => '999', 'content' => 'hi']),
    ]);

    $response = Laracord::bot()->messages()->create(['channel_id' => '1'], (new DiscordMessage)->content('hi'));

    expect($response->status())->toBe(200)
        ->and($response->json('id'))->toBe('999');
});

it('matches wildcard stubs', function (): void {
    Laracord::fake([
        'guilds.*' => Laracord::response(['id' => 'g1']),
        '*' => Laracord::response(['fallback' => true]),
    ]);

    expect(Laracord::bot()->guilds()->get(['guild_id' => '1'])->json('id'))->toBe('g1')
        ->and(Laracord::bot()->users()->getCurrent()->json('fallback'))->toBeTrue();
});

it('throws a stubbed throwable', function (): void {
    Laracord::fake([
        'messages.create' => new DiscordRateLimitException(1.5),
    ]);

    Laracord::bot()->messages()->create(['channel_id' => '1'], (new DiscordMessage)->content('x'));
})->throws(DiscordRateLimitException::class);

it('consumes sequenced responses in order', function (): void {
    Laracord::fake([
        'messages.create' => [
            Laracord::response(['id' => 'first']),
            Laracord::response(['id' => 'second']),
        ],
    ]);

    expect(Laracord::bot()->messages()->create(['channel_id' => '1'], (new DiscordMessage)->content('a'))->json('id'))->toBe('first')
        ->and(Laracord::bot()->messages()->create(['channel_id' => '1'], (new DiscordMessage)->content('b'))->json('id'))->toBe('second')
        ->and(Laracord::bot()->messages()->create(['channel_id' => '1'], (new DiscordMessage)->content('c'))->json('id'))->toBe('second');
});

it('asserts nothing was sent', function (): void {
    $fake = Laracord::fake();

    $fake->assertNothingSent();
});

it('resolves stubs from a closure receiving the request', function (): void {
    Laracord::fake([
        'channels.get' => fn (DiscordRequest $request): DiscordResponse => Laracord::response(['id' => $request->parameters['channel_id']]),
    ]);

    expect(Laracord::bot()->channels()->get(['channel_id' => '42'])->json('id'))->toBe('42');
});
