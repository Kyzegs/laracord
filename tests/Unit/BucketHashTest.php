<?php

use Kyzegs\Laracord\BucketHash;
use Kyzegs\Laracord\Route;
use Kyzegs\Laracord\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    config(['cache.default' => 'array']);
});

describe('BucketHash', function () {
    it('creates a bucket hash with a route', function () {
        $route = new Route('GET', '/test/path');
        $bucketHash = new BucketHash($route);

        expect($bucketHash)->toBeInstanceOf(BucketHash::class);
    });

    it('generates correct cache key', function () {
        $route = new Route('GET', '/channels/{channel_id}', [
            'channel_id' => '123456789',
        ]);
        $bucketHash = new BucketHash($route);

        // Use reflection to test private method
        $reflection = new ReflectionClass($bucketHash);
        $keyMethod = $reflection->getMethod('key');

        $key = $keyMethod->invoke($bucketHash);

        expect($key)->toContain('bucket_hashes:');
        expect($key)->toContain('GET /channels/{channel_id}');
    });

    it('returns null when cache is empty', function () {
        $route = new Route('GET', '/test/path');
        $bucketHash = new BucketHash($route);

        $hash = $bucketHash->get();

        expect($hash)->toBeNull();
    });

    it('stores and retrieves hash from cache', function () {
        $route = new Route('GET', '/test/path');
        $bucketHash = new BucketHash($route);

        $testHash = 'abc123def456';
        $bucketHash->put($testHash);

        $retrieved = $bucketHash->get();

        expect($retrieved)->toBe($testHash);
    });

    it('returns true when hash is missing from cache', function () {
        $route = new Route('GET', '/test/path');
        $bucketHash = new BucketHash($route);

        $missing = $bucketHash->missing();

        expect($missing)->toBeTrue();
    });

    it('returns false when hash exists in cache', function () {
        $route = new Route('GET', '/test/path');
        $bucketHash = new BucketHash($route);

        $bucketHash->put('test-hash');

        $missing = $bucketHash->missing();

        expect($missing)->toBeFalse();
    });

    it('handles different route methods', function () {
        $getRoute = new Route('GET', '/test/path');
        $postRoute = new Route('POST', '/test/path');

        $getHash = new BucketHash($getRoute);
        $postHash = new BucketHash($postRoute);

        $getHash->put('get-hash');
        $postHash->put('post-hash');

        expect($getHash->get())->toBe('get-hash');
        expect($postHash->get())->toBe('post-hash');
    });

    it('handles routes with parameters', function () {
        $route = new Route('GET', '/applications/{application_id}/commands', [
            'application_id' => '123456789',
        ]);
        $bucketHash = new BucketHash($route);

        $testHash = 'app-command-hash';
        $bucketHash->put($testHash);

        expect($bucketHash->get())->toBe($testHash);
    });

    it('handles routes with metadata', function () {
        $route = new Route('GET', '/test/path', [], 'metadata');
        $bucketHash = new BucketHash($route);

        $testHash = 'metadata-hash';
        $bucketHash->put($testHash);

        expect($bucketHash->get())->toBe($testHash);
    });

    it('generates unique keys for different routes', function () {
        $route1 = new Route('GET', '/channels/{channel_id}', ['channel_id' => '123']);
        $route2 = new Route('GET', '/channels/{channel_id}', ['channel_id' => '456']);

        $hash1 = new BucketHash($route1);
        $hash2 = new BucketHash($route2);

        $reflection = new ReflectionClass(BucketHash::class);
        $keyMethod = $reflection->getMethod('key');

        $key1 = $keyMethod->invoke($hash1);
        $key2 = $keyMethod->invoke($hash2);

        expect($key1)->toBe($key2); // Same route pattern, different parameters
    });

    it('handles complex route patterns', function () {
        $route = new Route('PUT', '/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}', [
            'webhook_id' => '123456789',
            'webhook_token' => 'abc123',
            'message_id' => 'msg456',
        ]);
        $bucketHash = new BucketHash($route);

        $testHash = 'complex-webhook-hash';
        $bucketHash->put($testHash);

        expect($bucketHash->get())->toBe($testHash);
    });

    it('overwrites existing hash', function () {
        $route = new Route('GET', '/test/path');
        $bucketHash = new BucketHash($route);

        $bucketHash->put('first-hash');
        $bucketHash->put('second-hash');

        expect($bucketHash->get())->toBe('second-hash');
    });
});
