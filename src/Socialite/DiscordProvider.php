<?php

namespace Kyzegs\Laracord\Socialite;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Two\AbstractProvider;

class DiscordProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * The cached user instance.
     *
     * @var \Kyzegs\Laracord\Socialite\User|null
     */
    protected $user;

    /**
     * The separating character for the requested scopes.
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * Create a new provider instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $clientId
     * @param  string  $clientSecret
     * @param  string  $redirectUrl
     * @param  array  $guzzle
     * @return void
     */
    public function __construct(Request $request, $clientId, $clientSecret, $redirectUrl, $guzzle = [])
    {
        parent::__construct($request, $clientId, $clientSecret, $redirectUrl, $guzzle);

        $this->setScopes(config('laracord.scopes'));
    }

    /**
     * Get the authentication URL for the provider.
     *
     * @param  string  $state
     * @return strin
     */
    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase('https://discord.com/api/oauth2/authorize', $state);
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl(): string
    {
        return 'https://discord.com/api/oauth2/token';
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param  string  $token
     * @return array
     */
    protected function getUserByToken(mixed $token): array
    {
        $response = $this->getHttpClient()->get('https://discord.com/api/users/@me', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param  array  $user
     * @return \Kyzegs\Laracord\Socialite\User
     */
    protected function mapUserToObject(array $user): User
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => sprintf('%s#%d', $user['username'], $user['discriminator']),
            'name' => $user['username'],
            'email' => $user['email'] ?? null,
            'avatar' => $user['avatar']
                ? sprintf('https://cdn.discordapp.com/avatars/%d/%s.png', $user['id'], $user['avatar'])
                : sprintf('https://cdn.discordapp.com/embed/avatars/%d.png', $user['discriminator']),
        ]);
    }
}
