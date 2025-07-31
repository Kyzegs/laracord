<?php

declare(strict_types=1);

use Illuminate\Contracts\Cache\Lock;
use Kyzegs\Laracord\Bucket;
use Kyzegs\Laracord\Ratelimit;
use Kyzegs\Laracord\Route;
use Kyzegs\Laracord\Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    config(['cache.default' => 'array']);
});

describe('Bucket', function (): void {
    it('creates a bucket with a route', function (): void {
        $route = new Route('GET', '/test/path');
        $bucket = new Bucket($route);

        expect($bucket)->toBeInstanceOf(Bucket::class);
    });

    it('generates correct cache key', function (): void {
        $route = new Route('GET', '/channels/{channel_id}', [
            'channel_id' => 123456789,
        ]);
        $bucket = new Bucket($route);

        // Use reflection to test private method
        $reflection = new ReflectionClass($bucket);
        $reflectionMethod = $reflection->getMethod('key');

        $key = $reflectionMethod->invoke($bucket);

        expect($key)->toContain('buckets:');
        expect($key)->toContain('123456789');
    });

    it('returns new ratelimit when cache is empty', function (): void {
        $route = new Route('GET', '/test/path');
        $bucket = new Bucket($route);

        $ratelimit = $bucket->get();

        expect($ratelimit)->toBeInstanceOf(Ratelimit::class);
        expect($ratelimit->getLimit())->toBe(1);
        expect($ratelimit->getRemaining())->toBe(1);
    });

    it('stores and retrieves ratelimit from cache', function (): void {
        $route = new Route('GET', '/test/path');
        $bucket = new Bucket($route);

        $ratelimit = new Ratelimit;
        $ratelimit->retry(5.0);

        $bucket->put($ratelimit);

        $retrieved = $bucket->get();

        expect($retrieved)->toBeInstanceOf(Ratelimit::class);
        expect($retrieved->getResetAfter())->toBeGreaterThanOrEqual(4.9);
        expect($retrieved->getResetAfter())->toBeLessThanOrEqual(5.1);
    });

    it('forgets ratelimit from cache', function (): void {
        $route = new Route('GET', '/test/path');
        $bucket = new Bucket($route);

        $ratelimit = new Ratelimit;
        $bucket->put($ratelimit);

        $result = $bucket->forget();

        expect($result)->toBeTrue();

        // Should return new ratelimit after forget
        $retrieved = $bucket->get();
        expect($retrieved->getLimit())->toBe(1);
    });

    it('creates a lock', function (): void {
        $route = new Route('GET', '/test/path');
        $bucket = new Bucket($route);

        $lock = $bucket->lock();

        expect($lock)->toBeInstanceOf(Lock::class);
    });

    it('handles bucket with major parameters', function (): void {
        $route = new Route('GET', '/applications/{application_id}/guilds/{guild_id}/commands', [
            'application_id' => 123456789,
            'guild_id' => 987654321,
        ]);
        $bucket = new Bucket($route);

        $ratelimit = new Ratelimit;
        $bucket->put($ratelimit);

        $retrieved = $bucket->get();
        expect($retrieved)->toBeInstanceOf(Ratelimit::class);
    });

    it('handles webhook routes', function (): void {
        $route = new Route('POST', '/webhooks/{webhook_id}/{webhook_token}', [
            'webhook_id' => 123456789,
            'webhook_token' => 'abc123',
        ]);
        $bucket = new Bucket($route);

        $ratelimit = new Ratelimit;
        $bucket->put($ratelimit);

        $retrieved = $bucket->get();
        expect($retrieved)->toBeInstanceOf(Ratelimit::class);
    });

    it('generates unique keys for different routes', function (): void {
        $route1 = new Route('GET', '/channels/{channel_id}', ['channel_id' => 123]);
        $route2 = new Route('GET', '/channels/{channel_id}', ['channel_id' => 456]);

        $bucket1 = new Bucket($route1);
        $bucket2 = new Bucket($route2);

        $reflection = new ReflectionClass(Bucket::class);
        $reflectionMethod = $reflection->getMethod('key');

        $key1 = $reflectionMethod->invoke($bucket1);
        $key2 = $reflectionMethod->invoke($bucket2);

        expect($key1)->not->toBe($key2);
    });

    it('handles routes without major parameters', function (): void {
        $route = new Route('GET', '/test/path');
        $bucket = new Bucket($route);

        $ratelimit = new Ratelimit;
        $bucket->put($ratelimit);

        $retrieved = $bucket->get();
        expect($retrieved)->toBeInstanceOf(Ratelimit::class);
    });
});
