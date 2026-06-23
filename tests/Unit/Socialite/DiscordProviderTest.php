<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Kyzegs\Laracord\Socialite\DiscordProvider;
use Kyzegs\Laracord\Socialite\User;

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
