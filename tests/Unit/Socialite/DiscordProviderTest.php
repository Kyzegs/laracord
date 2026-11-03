<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Kyzegs\Laracord\Socialite\DiscordProvider;
use Kyzegs\Laracord\Socialite\User;
use Laravel\Socialite\Two\InvalidStateException;

it('maps modern discord user names and default avatars', function (): void {
    $provider = new class(new Request, 'client', 'secret', 'https://example.test/callback') extends DiscordProvider
    {
        /** @param array<string, mixed> $user */
        public function map(array $user): User
        {
            return $this->mapUserToObject($user);
        }
    };

    $user = $provider->map([
        'id' => '123456789012345678',
        'username' => 'handle',
        'global_name' => 'Display Name',
        'discriminator' => '0',
        'avatar' => null,
    ]);

    expect($user->getId())->toBe('123456789012345678')
        ->and($user->getNickname())->toBe('handle')
        ->and($user->getName())->toBe('Display Name')
        ->and($user->getAvatar())->toContain('/embed/avatars/');
});

/** @param array<string, string> $query */
function oauthCallback(array $query, MockHandler $mock): DiscordProvider
{
    $provider = new DiscordProvider(new Request(['code' => 'abc', ...$query]), 'client', 'secret', 'https://example.test/callback');

    return $provider->stateless()->setHttpClient(new Client(['handler' => HandlerStack::create($mock)]));
}

function oauthResponses(): MockHandler
{
    return new MockHandler([
        new Response(200, [], '{"access_token":"token","scope":"identify"}'),
        new Response(200, [], '{"id":"1","username":"handle"}'),
    ]);
}

it('completes the OAuth flow when Discord is the issuer', function (): void {
    expect(oauthCallback(['iss' => 'https://discord.com'], oauthResponses())->user()->getId())->toBe('1');
});

it('rejects a code that Discord did not issue without exchanging it', function (array $query): void {
    $mock = oauthResponses();

    expect(fn () => oauthCallback($query, $mock)->user())->toThrow(InvalidStateException::class)
        ->and($mock->count())->toBe(2);
})->with([
    'another provider' => [['iss' => 'https://evil.test']],
    'no issuer' => [[]],
]);
