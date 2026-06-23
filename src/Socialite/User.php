<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Socialite;

use Illuminate\Support\Collection;
use Kyzegs\Laracord\ValueObjects\OAuthAccessToken;
use Laravel\Socialite\Two\User as AbstractUser;

class User extends AbstractUser
{
    /** @var Collection<int, PartialGuild> */
    public $guilds;

    /** @return Collection<int, PartialGuild> */
    public function getGuilds(bool $admin = false): Collection
    {
        if ($admin) {
            return $this->guilds->filter->isAdmin();
        }

        return $this->guilds;
    }

    /** @param Collection<int, PartialGuild> $guilds */
    public function setGuilds(Collection $guilds): void
    {
        $this->guilds = $guilds;
    }

    public function accessToken(): OAuthAccessToken
    {
        $expiresAt = $this->expiresIn === null ? null : new \DateTimeImmutable('+'.(int) $this->expiresIn.' seconds');

        return new OAuthAccessToken(
            (string) $this->token,
            $this->refreshToken === null ? null : (string) $this->refreshToken,
            $expiresAt,
            array_values($this->approvedScopes),
        );
    }
}
