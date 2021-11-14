<?php

namespace Kyzegs\Laracord\Socialite;

use Illuminate\Support\Collection;
use Laravel\Socialite\Two\User as AbstractUser;

class User extends AbstractUser
{
    /** @var \Illuminate\Support\Collection */
    public $guilds;

    /**
     * @param  bool  $admin
     * @return \Illuminate\Support\Collection
     */
    public function getGuilds(bool $admin = false): Collection
    {
        if ($admin) {
            return $this->guilds->filter->isAdmin();
        }

        return $this->guilds;
    }

    /**
     * @param  \Illuminate\Support\Collection  $guilds
     * @return void
     */
    public function setGuilds(Collection $guilds): void
    {
        $this->guilds = $guilds;
    }
}
