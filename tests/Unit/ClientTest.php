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

describe('Client', function (): void {
    it('retries server errors', function (): void {
        $mock = new MockHandler([
            new Response(500, ['Content-Type' => 'application/json'], '{}'),
            new Response(200, ['Content-Type' => 'application/json'], json_encode(['ok' => true])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(new RatelimitMiddleware);

        $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

        $data = $client->getChannel(1);

        expect($data)->toBe(['ok' => true]);
        Sleep::assertSleptTimes(1);
    });

    it('handles application command methods', function (): void {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                ['id' => 'cmd1', 'name' => 'test-command'],
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(new RatelimitMiddleware);

        $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

        $commands = $client->getGlobalApplicationCommands(123);

        expect($commands)->toBe([['id' => 'cmd1', 'name' => 'test-command']]);
    });

    it('handles guild application command methods', function (): void {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                ['id' => 'guild-cmd1', 'name' => 'guild-command'],
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(new RatelimitMiddleware);

        $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

        $commands = $client->getGuildApplicationCommands(123, 456);

        expect($commands)->toBe([['id' => 'guild-cmd1', 'name' => 'guild-command']]);
    });

    it('handles message creation', function (): void {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'id' => 'msg123',
                'content' => 'Hello World',
                'channel_id' => 123,
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(new RatelimitMiddleware);

        $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

        $message = $client->createMessage(123, ['content' => 'Hello World']);

        expect($message)->toBe([
            'id' => 'msg123',
            'content' => 'Hello World',
            'channel_id' => 123,
        ]);
    });

    it('handles channel operations', function (): void {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'id' => 123,
                'name' => 'test-channel',
                'type' => 0,
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(new RatelimitMiddleware);

        $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

        $channel = $client->getChannel(123);

        expect($channel)->toBe([
            'id' => 123,
            'name' => 'test-channel',
            'type' => 0,
        ]);
    });

    it('handles guild operations', function (): void {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'id' => 456,
                'name' => 'Test Guild',
                'member_count' => 100,
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(new RatelimitMiddleware);

        $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

        $guild = $client->getGuild(456);

        expect($guild)->toBe([
            'id' => 456,
            'name' => 'Test Guild',
            'member_count' => 100,
        ]);
    });

    it('handles user operations', function (): void {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'id' => 'user123',
                'username' => 'testuser',
                'discriminator' => '1234',
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(new RatelimitMiddleware);

        $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

        $user = $client->getCurrentUser();

        expect($user)->toBe([
            'id' => 'user123',
            'username' => 'testuser',
            'discriminator' => '1234',
        ]);
    });

    it('handles webhook operations', function (): void {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'id' => 123,
                'name' => 'Test Webhook',
                'channel_id' => 123,
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(new RatelimitMiddleware);

        $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

        $webhook = $client->getWebhook(123);

        expect($webhook)->toBe([
            'id' => 123,
            'name' => 'Test Webhook',
            'channel_id' => 123,
        ]);
    });

    it('handles emoji operations', function (): void {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                ['id' => 'emoji1', 'name' => 'smile', 'animated' => false],
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(new RatelimitMiddleware);

        $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

        $emojis = $client->listGuildEmojis(123);

        expect($emojis)->toBe([
            ['id' => 'emoji1', 'name' => 'smile', 'animated' => false],
        ]);
    });

    it('handles HTTP exceptions', function (): void {
        $mock = new MockHandler([
            new Response(404, ['Content-Type' => 'application/json'], json_encode([
                'message' => 'Unknown Channel',
                'code' => 10003,
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(new RatelimitMiddleware);

        $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

        expect(fn (): array => $client->getChannel(999))->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    });

    it('handles multiple retries for server errors', function (): void {
        $mock = new MockHandler([
            new Response(502, ['Content-Type' => 'application/json'], '{}'),
            new Response(504, ['Content-Type' => 'application/json'], '{}'),
            new Response(200, ['Content-Type' => 'application/json'], json_encode(['success' => true])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(new RatelimitMiddleware);

        $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

        $data = $client->getChannel(1);

        expect($data)->toBe(['success' => true]);
        Sleep::assertSleptTimes(2);
    });

    it('throws exception after max retries', function (): void {
        $mock = new MockHandler([
            new Response(500, ['Content-Type' => 'application/json'], '{}'),
            new Response(502, ['Content-Type' => 'application/json'], '{}'),
            new Response(524, ['Content-Type' => 'application/json'], '{}'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(new RatelimitMiddleware);

        $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

        expect(fn (): array => $client->getChannel(1))->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        Sleep::assertSleptTimes(3);
    });

    it('handles JSON decode errors gracefully', function (): void {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], 'invalid json'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(new RatelimitMiddleware);

        $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

        expect(fn (): array => $client->getChannel(1))->toThrow(\JsonException::class);
    });
});
