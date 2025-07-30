<?php

use Kyzegs\Laracord\Facades\Laracord;
use Kyzegs\Laracord\Tests\TestCase;

uses(TestCase::class);

describe('Laracord Facade', function () {
    it('extends base facade', function () {
        expect(Laracord::class)->toExtend(\Illuminate\Support\Facades\Facade::class);
    });

    it('has correct facade accessor', function () {
        $reflection = new ReflectionClass(Laracord::class);
        $method = $reflection->getMethod('getFacadeAccessor');

        $accessor = $method->invoke(null);

        expect($accessor)->toBe('laracord');
    });

    it('can be resolved from container', function () {
        $this->app->singleton('laracord', function () {
            return new \Kyzegs\Laracord\Client(
                new \GuzzleHttp\Client
            );
        });

        $client = Laracord::getFacadeRoot();

        expect($client)->toBeInstanceOf(\Kyzegs\Laracord\Client::class);
    });

    it('forwards calls to underlying client', function () {
        $mockClient = Mockery::mock(\Kyzegs\Laracord\Client::class);
        $mockClient->shouldReceive('getChannel')
            ->with(123)
            ->once()
            ->andReturn(['id' => 123, 'name' => 'test-channel']);

        $this->app->singleton('laracord', function () use ($mockClient) {
            return $mockClient;
        });

        $result = Laracord::getChannel(123);

        expect($result)->toBe(['id' => 123, 'name' => 'test-channel']);
    });

    it('handles message methods', function () {
        $mockClient = Mockery::mock(\Kyzegs\Laracord\Client::class);
        $mockClient->shouldReceive('createMessage')
            ->with(123, ['content' => 'Hello World'])
            ->once()
            ->andReturn(['id' => 'msg123', 'content' => 'Hello World']);

        $this->app->singleton('laracord', function () use ($mockClient) {
            return $mockClient;
        });

        $message = Laracord::createMessage(123, ['content' => 'Hello World']);

        expect($message)->toBe(['id' => 'msg123', 'content' => 'Hello World']);
    });

    it('handles webhook methods', function () {
        $mockClient = Mockery::mock(\Kyzegs\Laracord\Client::class);
        $mockClient->shouldReceive('executeWebhook')
            ->with(123, 'token456', ['content' => 'Webhook message'])
            ->once()
            ->andReturn(['id' => 'webhook-msg']);

        $this->app->singleton('laracord', function () use ($mockClient) {
            return $mockClient;
        });

        $result = Laracord::executeWebhook(123, 'token456', ['content' => 'Webhook message']);

        expect($result)->toBe(['id' => 'webhook-msg']);
    });

    it('handles user methods', function () {
        $mockClient = Mockery::mock(\Kyzegs\Laracord\Client::class);
        $mockClient->shouldReceive('getCurrentUser')
            ->once()
            ->andReturn(['id' => 'user123', 'username' => 'testuser']);

        $this->app->singleton('laracord', function () use ($mockClient) {
            return $mockClient;
        });

        $user = Laracord::getCurrentUser();

        expect($user)->toBe(['id' => 'user123', 'username' => 'testuser']);
    });

    it('handles channel methods', function () {
        $mockClient = Mockery::mock(\Kyzegs\Laracord\Client::class);
        $mockClient->shouldReceive('getChannel')
            ->with(123)
            ->once()
            ->andReturn(['id' => 123, 'type' => 0, 'name' => 'general']);

        $this->app->singleton('laracord', function () use ($mockClient) {
            return $mockClient;
        });

        $channel = Laracord::getChannel(123);

        expect($channel)->toBe(['id' => 123, 'type' => 0, 'name' => 'general']);
    });

    it('handles emoji methods', function () {
        $mockClient = Mockery::mock(\Kyzegs\Laracord\Client::class);
        $mockClient->shouldReceive('listGuildEmojis')
            ->with(123)
            ->once()
            ->andReturn([['id' => 'emoji1', 'name' => 'smile']]);

        $this->app->singleton('laracord', function () use ($mockClient) {
            return $mockClient;
        });

        $emojis = Laracord::listGuildEmojis(123);

        expect($emojis)->toBe([['id' => 'emoji1', 'name' => 'smile']]);
    });
});
