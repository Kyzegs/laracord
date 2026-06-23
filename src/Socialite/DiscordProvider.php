<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Socialite;

use Illuminate\Support\Collection;
use Laravel\Socialite\Two\AbstractProvider;

class DiscordProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * The cached user instance.
     *
     * @var User|null
     */
    protected $user;

    /**
     * The separating character for the requested scopes.
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * Get the authentication URL for the provider.
     *
     * @param  string  $state
     */
    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase('https://discord.com/oauth2/authorize', $state);
    }

    /**
     * Get the token URL for the provider.
     */
    protected function getTokenUrl(): string
    {
        return 'https://discord.com/api/v10/oauth2/token';
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param  string  $token
     */
    /** @return array<string, mixed> */
    protected function getUserByToken(mixed $token): array
    {
        $response = $this->getHttpClient()->get('https://discord.com/api/v10/users/@me', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Map the raw user array to a Socialite User instance.
     */
    /** @param array<string, mixed> $user */
    protected function mapUserToObject(array $user): User
    {
        $id = (string) $user['id'];
        $discriminator = (string) ($user['discriminator'] ?? '0');
        $defaultAvatar = $discriminator === '0' ? (((int) $id >> 22) % 6) : ((int) $discriminator % 5);

        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => $user['username'],
            'name' => $user['global_name'] ?? $user['username'],
            'email' => $user['email'] ?? null,
            'avatar' => isset($user['avatar'])
                ? sprintf('https://cdn.discordapp.com/avatars/%s/%s.png', $id, $user['avatar'])
                : sprintf('https://cdn.discordapp.com/embed/avatars/%d.png', $defaultAvatar),
        ]);
    }

    /** @return Collection<int, PartialGuild> */
    public function guildsFromToken(string $token): Collection
    {
        $response = $this->getHttpClient()->get('https://discord.com/api/v10/users/@me/guilds', [
            'headers' => ['Authorization' => 'Bearer '.$token],
        ]);

        /** @var list<array<string, mixed>> $guilds */
        $guilds = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return collect($guilds)
            ->map(static fn (array $guild): PartialGuild => new PartialGuild(
                (string) $guild['id'],
                (string) $guild['name'],
                $guild['icon'] ?? null,
                (bool) ($guild['owner'] ?? false),
                (int) ($guild['permissions'] ?? 0),
                array_values($guild['features'] ?? []),
            ));
    }
}
