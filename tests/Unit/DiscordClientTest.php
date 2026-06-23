<?php

declare(strict_types=1);

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Kyzegs\Laracord\ClientFactory;
use Kyzegs\Laracord\Enums\AuthenticationRequirement;
use Kyzegs\Laracord\Enums\HttpMethod;
use Kyzegs\Laracord\Exceptions\DiscordNotFoundException;
use Kyzegs\Laracord\Http\DiscordRequest;
use Kyzegs\Laracord\Tests\TestCase;
use Kyzegs\Laracord\ValueObjects\Authentication;

uses(TestCase::class);

it('sends bot auth and discord query encoding', function (): void {
    $history = [];
    $handlerStack = HandlerStack::create(new MockHandler([
        new Response(200, ['Content-Type' => 'application/json'], '{"ok":true}'),
    ]));
    $handlerStack->push(Middleware::history($history));

    $discordClient = resolve(ClientFactory::class)->make(Authentication::bot('secret'), $handlerStack);

    $discordResponse = $discordClient->send(new DiscordRequest(HttpMethod::GET, '/channels/{channel_id}', ['channel_id' => '123'], [
        'id' => ['1', '2'],
        'enabled' => true,
    ]));

    expect($discordResponse->json('ok'))->toBeTrue()
        ->and($history[0]['request']->getHeaderLine('Authorization'))->toBe('Bot secret')
        ->and($history[0]['request']->getUri()->getPath())->toBe('/api/v10/channels/123')
        ->and($history[0]['request']->getUri()->getQuery())->toBe('id=1&id=2&enabled=true');
});

it('supports empty unauthenticated responses', function (): void {
    $handlerStack = HandlerStack::create(new MockHandler([new Response(204)]));
    $discordClient = resolve(ClientFactory::class)->make(Authentication::none(), $handlerStack);

    $discordResponse = $discordClient->send(new DiscordRequest(HttpMethod::DELETE, '/webhooks/1/token', authentication: AuthenticationRequirement::NONE));

    expect($discordResponse->isNoContent())->toBeTrue();
});

it('throws typed discord exceptions', function (): void {
    $handlerStack = HandlerStack::create(new MockHandler([new Response(404, [], '{"message":"Unknown Channel"}')]));
    $discordClient = resolve(ClientFactory::class)->make(Authentication::bot('secret'), $handlerStack);

    expect(fn () => $discordClient->send(new DiscordRequest(HttpMethod::GET, '/channels/404')))
        ->toThrow(DiscordNotFoundException::class, 'Unknown Channel');
});
