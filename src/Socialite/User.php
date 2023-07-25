<?php

namespace Kyzegs\Laracord\Socialite;

use Illuminate\Support\Collection;
use Laravel\Socialite\Two\User as AbstractUser;

class User extends AbstractUser
{
    /** @var \Illuminate\Support\Collection */
    public $guilds;

    public function getGuilds(bool $admin = false): Collection
    {
        if ($admin) {
            return $this->guilds->filter->isAdmin();
        }

        return $this->guilds;
    }

    public function setGuilds(Collection $guilds): void
    {
        $this->guilds = $guilds;
    }
}
