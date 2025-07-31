<?php

declare(strict_types=1);

use Kyzegs\Laracord\Client;
use Kyzegs\Laracord\ServiceProvider;
use Kyzegs\Laracord\Tests\TestCase;

uses(TestCase::class);

describe('ServiceProvider', function (): void {
    it('registers the service provider', function (): void {
        $provider = new ServiceProvider($this->app);

        expect($provider)->toBeInstanceOf(ServiceProvider::class);
    });

    it('registers client as singleton', function (): void {
        $provider = new ServiceProvider($this->app);
        $provider->register();

        $client1 = $this->app->make(Client::class);
        $client2 = $this->app->make(Client::class);

        expect($client1)->toBeInstanceOf(Client::class);
        expect($client2)->toBeInstanceOf(Client::class);
        expect($client1)->toBe($client2); // Should be the same instance (singleton)
    });

    it('registers laracord alias', function (): void {
        $provider = new ServiceProvider($this->app);
        $provider->register();

        $client = $this->app->make('laracord');

        expect($client)->toBeInstanceOf(Client::class);
    });

    it('merges configuration', function (): void {
        $provider = new ServiceProvider($this->app);
        $provider->register();

        $config = config('laracord');

        expect($config)->toBeArray();
        expect($config)->toHaveKey('bot_token');
    });

    it('provides correct services', function (): void {
        $provider = new ServiceProvider($this->app);

        $provides = $provider->provides();

        expect($provides)->toContain(Client::class);
        expect($provides)->toContain('laracord');
    });

    it('implements deferrable provider', function (): void {
        $provider = new ServiceProvider($this->app);

        expect($provider)->toBeInstanceOf(\Illuminate\Contracts\Support\DeferrableProvider::class);
    });

    it('extends base service provider', function (): void {
        $provider = new ServiceProvider($this->app);

        expect($provider)->toBeInstanceOf(\Illuminate\Support\ServiceProvider::class);
    });

    it('creates client with factory', function (): void {
        $provider = new ServiceProvider($this->app);
        $provider->register();

        $client = $this->app->make(Client::class);

        expect($client)->toBeInstanceOf(Client::class);

        // Verify it's created through the factory
        $reflection = new ReflectionClass($client);
        $reflectionProperty = $reflection->getProperty('guzzleClient');
        $guzzleClient = $reflectionProperty->getValue($client);

        expect($guzzleClient)->toBeInstanceOf(\GuzzleHttp\Client::class);
    });

    it('handles multiple service provider instances', function (): void {
        $provider1 = new ServiceProvider($this->app);
        $provider2 = new ServiceProvider($this->app);

        expect($provider1)->toBeInstanceOf(ServiceProvider::class);
        expect($provider2)->toBeInstanceOf(ServiceProvider::class);
        expect($provider1)->not->toBe($provider2);
    });

    it('registers services in correct order', function (): void {
        $provider = new ServiceProvider($this->app);

        // Register should not throw
        expect(fn () => $provider->register())->not->toThrow(\Exception::class);

        // Boot should not throw
        expect(fn () => $provider->boot())->not->toThrow(\Exception::class);
    });
});
