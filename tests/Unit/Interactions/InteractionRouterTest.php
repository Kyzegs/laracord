<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Kyzegs\Laracord\Interactions\Interaction;
use Kyzegs\Laracord\Interactions\InteractionResponse;
use Kyzegs\Laracord\Interactions\InteractionRouter;
use Kyzegs\Laracord\Interactions\UnhandledInteractionException;
use Kyzegs\Laracord\Tests\TestCase;

uses(TestCase::class);

function router(): InteractionRouter
{
    return resolve(InteractionRouter::class);
}

it('auto-answers a ping', function (): void {
    $response = router()->handle(new Interaction(['type' => 1]));

    expect($response->getStatusCode())->toBe(200)
        ->and(json_decode((string) $response->getContent(), true))->toBe(['type' => 1]);
});

it('dispatches an application command by name', function (): void {
    $router = router()->command('ban', fn (Interaction $interaction): JsonResponse => InteractionResponse::message([
        'content' => 'Banned '.$interaction->option('user'),
    ]));

    $response = $router->handle(new Interaction([
        'type' => 2,
        'data' => ['name' => 'ban', 'options' => [['name' => 'user', 'value' => 'Bob']]],
    ]));

    expect(json_decode((string) $response->getContent(), true))
        ->toBe(['type' => 4, 'data' => ['content' => 'Banned Bob']]);
});

it('matches a wildcard component custom_id and passes captures', function (): void {
    $router = router()->component('vote:*', fn (Interaction $interaction, array $parameters): JsonResponse => InteractionResponse::message([
        'content' => 'voted '.$parameters[0],
    ]));

    $response = $router->handle(new Interaction(['type' => 3, 'data' => ['custom_id' => 'vote:42']]));

    expect(json_decode((string) $response->getContent(), true))
        ->toBe(['type' => 4, 'data' => ['content' => 'voted 42']]);
});

it('dispatches a modal submit', function (): void {
    $router = router()->modal('feedback', fn (Interaction $interaction): JsonResponse => InteractionResponse::message(['content' => 'thanks']));

    $response = $router->handle(new Interaction(['type' => 5, 'data' => ['custom_id' => 'feedback']]));

    expect(json_decode((string) $response->getContent(), true))
        ->toBe(['type' => 4, 'data' => ['content' => 'thanks']]);
});

it('dispatches autocomplete interactions', function (): void {
    $router = router()->autocomplete('search', fn (Interaction $interaction): JsonResponse => InteractionResponse::autocomplete([
        ['name' => 'first', 'value' => '1'],
    ]));

    $response = $router->handle(new Interaction(['type' => 4, 'data' => ['name' => 'search']]));

    expect(json_decode((string) $response->getContent(), true))
        ->toBe(['type' => 8, 'data' => ['choices' => [['name' => 'first', 'value' => '1']]]]);
});

it('resolves invokable class handlers through the container', function (): void {
    $router = router()->command('ping', PingCommand::class);

    $response = $router->handle(new Interaction(['type' => 2, 'data' => ['name' => 'ping']]));

    expect(json_decode((string) $response->getContent(), true))
        ->toBe(['type' => 4, 'data' => ['content' => 'pong']]);
});

it('builds the interaction from a request body', function (): void {
    $router = router()->command('hi', fn (Interaction $interaction): JsonResponse => InteractionResponse::message(['content' => $interaction->id()]));

    $request = Request::create('/discord', 'POST', server: ['CONTENT_TYPE' => 'application/json'], content: (string) json_encode([
        'id' => 'abc',
        'type' => 2,
        'data' => ['name' => 'hi'],
    ]));

    $response = $router->handle($request);

    expect(json_decode((string) $response->getContent(), true))
        ->toBe(['type' => 4, 'data' => ['content' => 'abc']]);
});

it('throws when no handler is registered', function (): void {
    router()->handle(new Interaction(['type' => 2, 'data' => ['name' => 'unknown']]));
})->throws(UnhandledInteractionException::class);

final class PingCommand
{
    public function __invoke(): JsonResponse
    {
        return InteractionResponse::message(['content' => 'pong']);
    }
}
