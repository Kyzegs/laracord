<?php

declare(strict_types=1);

use Kyzegs\Laracord\Client;
use Kyzegs\Laracord\Factory;
use Kyzegs\Laracord\Laracord;
use Kyzegs\Laracord\Tests\TestCase;

uses(TestCase::class);

describe('Laracord', function (): void {
    it('creates a factory instance', function (): void {
        $factory = Laracord::factory();

        expect($factory)->toBeInstanceOf(Factory::class);
    });

    it('creates a client instance', function (): void {
        config(['laracord.bot_token' => 'test-bot-token']);

        $client = Laracord::client();

        expect($client)->toBeInstanceOf(Client::class);
    });

    it('creates client through factory', function (): void {
        config(['laracord.bot_token' => 'test-bot-token']);

        $factory = Laracord::factory();
        $clientFromFactory = $factory->make();
        $clientDirect = Laracord::client();

        expect($clientFromFactory)->toBeInstanceOf(Client::class);
        expect($clientDirect)->toBeInstanceOf(Client::class);
    });

    it('creates multiple factory instances', function (): void {
        $factory1 = Laracord::factory();
        $factory2 = Laracord::factory();

        expect($factory1)->toBeInstanceOf(Factory::class);
        expect($factory2)->toBeInstanceOf(Factory::class);
        expect($factory1)->not->toBe($factory2);
    });

    it('creates multiple client instances', function (): void {
        config(['laracord.bot_token' => 'test-bot-token']);

        $client1 = Laracord::client();
        $client2 = Laracord::client();

        expect($client1)->toBeInstanceOf(Client::class);
        expect($client2)->toBeInstanceOf(Client::class);
        expect($client1)->not->toBe($client2);
    });

    it('factory creates properly configured client', function (): void {
        config(['laracord.bot_token' => 'test-bot-token']);

        $factory = Laracord::factory();
        $client = $factory->make();

        expect($client)->toBeInstanceOf(Client::class);

        // Verify the client has a properly configured Guzzle client
        $reflection = new ReflectionClass($client);
        $reflectionProperty = $reflection->getProperty('guzzleClient');
        $guzzleClient = $reflectionProperty->getValue($client);

        expect($guzzleClient)->toBeInstanceOf(\GuzzleHttp\Client::class);
    });

    it('handles empty bot token in client creation', function (): void {
        config(['laracord.bot_token' => '']);

        $client = Laracord::client();

        expect($client)->toBeInstanceOf(Client::class);
    });

    it('handles null bot token in client creation', function (): void {
        config(['laracord.bot_token' => null]);

        $client = Laracord::client();

        expect($client)->toBeInstanceOf(Client::class);
    });

    it('factory and client methods are consistent', function (): void {
        config(['laracord.bot_token' => 'test-bot-token']);

        $factoryClient = Laracord::factory()->make();
        $directClient = Laracord::client();

        expect($factoryClient)->toBeInstanceOf(Client::class);
        expect($directClient)->toBeInstanceOf(Client::class);
    });
});
