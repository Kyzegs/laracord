<?php

namespace Kyzegs\Laracord\Socialite;

class PartialGuild
{
    /**
     * @param int $id
     * @param string $name
     * @param string $icon
     * @param bool $owner
     * @param int $permissions
     * @param array $features
     * @return void
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $icon,
        public bool $owner,
        public int $permissions,
        public array $features,
    ) { }

    /**
     * Return whether or not the user is an administrator.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return ($this->permissions & 0x8) == 0x8;
    }

    /**
     * Determine what role a user has within the given guild.
     *
     * @return string
     */
    public function role(): string
    {
        if ($this->owner) {
            return 'Owner';
        } else if ($this->isAdmin()) {
            return 'Administrator';
        } else {
            return 'Member';
        }
    }
}
