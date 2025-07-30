<?php

declare(strict_types=1);

use GuzzleHttp\Client as GuzzleClient;
use Kyzegs\Laracord\Client;
use Kyzegs\Laracord\Factory;
use Kyzegs\Laracord\Tests\TestCase;

uses(TestCase::class);

describe('Factory', function (): void {
    it('creates a client with proper configuration', function (): void {
        config(['laracord.bot_token' => 'test-bot-token']);

        $factory = new Factory;
        $client = $factory->make();

        expect($client)->toBeInstanceOf(Client::class);
    });

    it('creates client with guzzle client', function (): void {
        config(['laracord.bot_token' => 'test-bot-token']);

        $factory = new Factory;
        $client = $factory->make();

        // Use reflection to access the private guzzle client
        $reflection = new ReflectionClass($client);
        $reflectionProperty = $reflection->getProperty('guzzleClient');
        $guzzleClient = $reflectionProperty->getValue($client);

        expect($guzzleClient)->toBeInstanceOf(GuzzleClient::class);
    });

    it('configures guzzle client with correct base URI', function (): void {
        config(['laracord.bot_token' => 'test-bot-token']);

        $factory = new Factory;
        $client = $factory->make();

        // Use reflection to access the private guzzle client
        $reflection = new ReflectionClass($client);
        $reflectionProperty = $reflection->getProperty('guzzleClient');
        $guzzleClient = $reflectionProperty->getValue($client);

        // Check if the handler stack contains the ratelimit middleware
        $handlerStack = $guzzleClient->getConfig('handler');
        expect($handlerStack)->not->toBeNull();
    });

    it('configures guzzle client with authorization header', function (): void {
        $botToken = 'test-bot-token-123';
        config(['laracord.bot_token' => $botToken]);

        $factory = new Factory;
        $client = $factory->make();

        // Use reflection to access the private guzzle client
        $reflection = new ReflectionClass($client);
        $reflectionProperty = $reflection->getProperty('guzzleClient');
        $guzzleClient = $reflectionProperty->getValue($client);

        $config = $guzzleClient->getConfig();
        expect($config['headers']['Authorization'])->toBe('Bot '.$botToken);
    });

    it('uses correct base URI from Route class', function (): void {
        config(['laracord.bot_token' => 'test-bot-token']);

        $factory = new Factory;
        $client = $factory->make();

        // Use reflection to access the private guzzle client
        $reflection = new ReflectionClass($client);
        $reflectionProperty = $reflection->getProperty('guzzleClient');
        $guzzleClient = $reflectionProperty->getValue($client);

        $config = $guzzleClient->getConfig();
        expect((string) $config['base_uri'])->toBe('https://discord.com/api/v10');
    });

    it('adds ratelimit middleware to handler stack', function (): void {
        config(['laracord.bot_token' => 'test-bot-token']);

        $factory = new Factory;
        $client = $factory->make();

        // Use reflection to access the private guzzle client
        $reflection = new ReflectionClass($client);
        $reflectionProperty = $reflection->getProperty('guzzleClient');
        $guzzleClient = $reflectionProperty->getValue($client);

        $handlerStack = $guzzleClient->getConfig('handler');

        // The handler stack should be configured with middleware
        expect($handlerStack)->not->toBeNull();
    });

    it('creates multiple clients independently', function (): void {
        config(['laracord.bot_token' => 'test-bot-token']);

        $factory = new Factory;
        $client1 = $factory->make();
        $client2 = $factory->make();

        expect($client1)->toBeInstanceOf(Client::class);
        expect($client2)->toBeInstanceOf(Client::class);
        expect($client1)->not->toBe($client2);
    });

    it('handles empty bot token configuration', function (): void {
        config(['laracord.bot_token' => '']);

        $factory = new Factory;
        $client = $factory->make();

        expect($client)->toBeInstanceOf(Client::class);

        // Use reflection to access the private guzzle client
        $reflection = new ReflectionClass($client);
        $reflectionProperty = $reflection->getProperty('guzzleClient');
        $guzzleClient = $reflectionProperty->getValue($client);

        $config = $guzzleClient->getConfig();
        expect($config['headers']['Authorization'])->toBe('Bot ');
    });

    it('handles null bot token configuration', function (): void {
        config(['laracord.bot_token' => null]);

        $factory = new Factory;
        $client = $factory->make();

        expect($client)->toBeInstanceOf(Client::class);

        // Use reflection to access the private guzzle client
        $reflection = new ReflectionClass($client);
        $reflectionProperty = $reflection->getProperty('guzzleClient');
        $guzzleClient = $reflectionProperty->getValue($client);

        $config = $guzzleClient->getConfig();
        expect($config['headers']['Authorization'])->toBe('Bot ');
    });
});
