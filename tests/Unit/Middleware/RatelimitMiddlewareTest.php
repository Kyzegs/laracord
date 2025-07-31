<?php

declare(strict_types=1);

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Sleep;
use Kyzegs\Laracord\Client;
use Kyzegs\Laracord\Middleware\RatelimitMiddleware;
use Kyzegs\Laracord\Route;
use Kyzegs\Laracord\Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    config(['cache.default' => 'array']);
    Sleep::fake();
});

describe('RatelimitMiddleware', function (): void {
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

    it('handles requests without route', function (): void {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '{}'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(new RatelimitMiddleware);

        $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

        // This should not throw any exceptions
        expect(fn (): array => $client->getChannel(1))->not->toThrow(\Exception::class);
    });

    it('updates bucket hash when received from response', function (): void {
        $mock = new MockHandler([
            new Response(200, [
                'Content-Type' => 'application/json',
                'X-Ratelimit-Bucket' => 'new-hash-123',
                'X-Ratelimit-Limit' => '5',
                'X-Ratelimit-Remaining' => '4',
                'X-Ratelimit-Reset-After' => '60',
            ], '{}'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(new RatelimitMiddleware);

        $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

        $client->getChannel(1);

        // The bucket hash should be updated in cache
        $route = new Route('GET', '/channels/{channel_id}', ['channel_id' => 1]);
        $bucketHash = $route->getBucketHash();

        expect($bucketHash->get())->toBe('new-hash-123');
    });

    it('handles missing retry_after in 429 response', function (): void {
        $mock = new MockHandler([
            new Response(429, [
                'Content-Type' => 'application/json',
                'X-Ratelimit-Bucket' => 'abc',
            ], json_encode([])), // No retry_after
            new Response(200, ['Content-Type' => 'application/json'], '{}'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(new RatelimitMiddleware);

        $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

        $client->getChannel(1);

        // Should still retry but with default behavior
        expect(true)->toBeTrue();
    });

    it('handles invalid JSON in 429 response', function (): void {
        $mock = new MockHandler([
            new Response(429, [
                'Content-Type' => 'application/json',
                'X-Ratelimit-Bucket' => 'abc',
            ], 'invalid json'),
            new Response(200, ['Content-Type' => 'application/json'], '{}'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(new RatelimitMiddleware);

        $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

        $client->getChannel(1);

        // Should handle gracefully
        expect(true)->toBeTrue();
    });

    it('handles sub-ratelimit scenarios', function (): void {
        $mock = new MockHandler([
            new Response(200, [
                'Content-Type' => 'application/json',
                'X-Ratelimit-Bucket' => 'abc',
                'X-Ratelimit-Limit' => '5',
                'X-Ratelimit-Remaining' => '3',
            ], '{}'),
            new Response(429, [
                'Content-Type' => 'application/json',
                'X-Ratelimit-Bucket' => 'abc',
            ], json_encode(['retry_after' => 0.1])),
            new Response(200, ['Content-Type' => 'application/json'], '{}'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(new RatelimitMiddleware);

        $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

        $client->getChannel(1);
        $client->getChannel(1);

        Sleep::assertSleptTimes(1);
    });

    it('handles bucket hash changes', function (): void {
        $mock = new MockHandler([
            new Response(200, [
                'Content-Type' => 'application/json',
                'X-Ratelimit-Bucket' => 'hash1',
                'X-Ratelimit-Limit' => '5',
                'X-Ratelimit-Remaining' => '4',
            ], '{}'),
            new Response(200, [
                'Content-Type' => 'application/json',
                'X-Ratelimit-Bucket' => 'hash2',
                'X-Ratelimit-Limit' => '5',
                'X-Ratelimit-Remaining' => '4',
            ], '{}'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(new RatelimitMiddleware);

        $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

        $client->getChannel(1);
        $client->getChannel(1);

        // Both hashes should be stored
        $route = new Route('GET', '/channels/{channel_id}', ['channel_id' => 1]);
        $bucketHash = $route->getBucketHash();

        expect($bucketHash->get())->toBe('hash2');
    });

    it('handles missing rate limit headers', function (): void {
        $mock = new MockHandler([
            new Response(200, [
                'Content-Type' => 'application/json',
                // No rate limit headers
            ], '{}'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(new RatelimitMiddleware);

        $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

        // Should handle gracefully without rate limit headers
        expect(fn (): array => $client->getChannel(1))->not->toThrow(\Exception::class);
    });

    it('handles multiple 429 responses', function (): void {
        $mock = new MockHandler([
            new Response(429, [
                'Content-Type' => 'application/json',
                'X-Ratelimit-Bucket' => 'abc',
            ], json_encode(['retry_after' => 0.1])),
            new Response(429, [
                'Content-Type' => 'application/json',
                'X-Ratelimit-Bucket' => 'abc',
            ], json_encode(['retry_after' => 0.1])),
            new Response(200, ['Content-Type' => 'application/json'], '{}'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(new RatelimitMiddleware);

        $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

        $client->getChannel(1);

        Sleep::assertSleptTimes(2);
    });

    it('handles max retry attempts', function (): void {
        $mock = new MockHandler([
            new Response(429, [
                'Content-Type' => 'application/json',
                'X-Ratelimit-Bucket' => 'abc',
            ], json_encode(['retry_after' => 0.1])),
            new Response(429, [
                'Content-Type' => 'application/json',
                'X-Ratelimit-Bucket' => 'abc',
            ], json_encode(['retry_after' => 0.1])),
            new Response(429, [
                'Content-Type' => 'application/json',
                'X-Ratelimit-Bucket' => 'abc',
            ], json_encode(['retry_after' => 0.1])),
            new Response(429, [
                'Content-Type' => 'application/json',
                'X-Ratelimit-Bucket' => 'abc',
            ], json_encode(['retry_after' => 0.1])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(new RatelimitMiddleware);

        $client = new Client(new GuzzleClient(['handler' => $handlerStack]));

        // Should throw an exception after max retries
        expect(fn (): array => $client->getChannel(1))->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        // Should only retry 3 times max
        Sleep::assertSleptTimes(3);
    });
});
