<?php

declare(strict_types=1);

use Kyzegs\Laracord\Route;
use Kyzegs\Laracord\Tests\TestCase;

uses(TestCase::class);

describe('Route', function (): void {
    it('creates a route with basic parameters', function (): void {
        $route = new Route('GET', '/test/path');

        expect($route->getMethod())->toBe('GET');
        expect($route->getUrl())->toBe('https://discord.com/api/v10/test/path');
        expect($route->getKey())->toBe('GET /test/path');
    });

    it('creates a route with parameters', function (): void {
        $route = new Route('POST', '/applications/{application_id}/commands', [
            'application_id' => 123456789,
        ]);

        expect($route->getMethod())->toBe('POST');
        expect($route->getUrl())->toBe('https://discord.com/api/v10/applications/123456789/commands');
        expect($route->getKey())->toBe('POST /applications/{application_id}/commands');
    });

    it('creates a route with metadata', function (): void {
        $route = new Route('GET', '/test/path', [], 'metadata');

        expect($route->getKey())->toBe('GET /test/path:metadata');
    });

    it('formats path with multiple parameters', function (): void {
        $route = new Route('PATCH', '/applications/{application_id}/commands/{command_id}', [
            'application_id' => 123456789,
            'command_id' => 987654321,
        ]);

        expect($route->getUrl())->toBe('https://discord.com/api/v10/applications/123456789/commands/987654321');
    });

    it('handles major parameters correctly', function (): void {
        $route = new Route('GET', '/channels/{channel_id}', [
            'channel_id' => 123456789,
        ]);

        expect($route->getMajorParameters())->toBe('123456789');
    });

    it('handles multiple major parameters', function (): void {
        $route = new Route('GET', '/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}', [
            'webhook_id' => 123456789,
            'webhook_token' => 'abc123',
            'message_id' => 'msg456',
        ]);

        expect($route->getMajorParameters())->toBe('123456789+abc123');
    });

    it('handles webhook parameters', function (): void {
        $route = new Route('POST', '/webhooks/{webhook_id}/{webhook_token}', [
            'webhook_id' => 123456789,
            'webhook_token' => 'abc123',
        ]);

        expect($route->getMajorParameters())->toBe('123456789+abc123');
    });

    it('filters out null major parameters', function (): void {
        $route = new Route('GET', '/test/path', []);

        expect($route->getMajorParameters())->toBe('');
    });

    it('creates bucket instance', function (): void {
        $route = new Route('GET', '/test/path');
        $bucket = $route->getBucket();

        expect($bucket)->toBeInstanceOf(\Kyzegs\Laracord\Bucket::class);
    });

    it('creates bucket hash instance', function (): void {
        $route = new Route('GET', '/test/path');
        $bucketHash = $route->getBucketHash();

        expect($bucketHash)->toBeInstanceOf(\Kyzegs\Laracord\BucketHash::class);
    });

    it('handles complex route with all parameter types', function (): void {
        $route = new Route('PUT', '/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}', [
            'webhook_id' => 123456789,
            'webhook_token' => 'abc123',
            'message_id' => 'msg456',
        ], 'complex');

        expect($route->getMethod())->toBe('PUT');
        expect($route->getUrl())->toBe('https://discord.com/api/v10/webhooks/123456789/abc123/messages/msg456');
        expect($route->getKey())->toBe('PUT /webhooks/{webhook_id}/{webhook_token}/messages/{message_id}:complex');
        expect($route->getMajorParameters())->toBe('123456789+abc123');
    });
});
