<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Socialite;

use Kyzegs\Laracord\ValueObjects\OAuthAccessToken;
use Laravel\Socialite\Two\User as AbstractUser;

class User extends AbstractUser
{
    public function accessToken(): OAuthAccessToken
    {
        $expiresAt = $this->expiresIn === null ? null : new \DateTimeImmutable('+'.(int) $this->expiresIn.' seconds');

        return new OAuthAccessToken(
            (string) $this->token,
            $this->refreshToken === null ? null : (string) $this->refreshToken,
            $expiresAt,
            array_values((array) $this->approvedScopes),
        );
    }
}
