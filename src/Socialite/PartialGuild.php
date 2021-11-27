<?php

namespace Kyzegs\Laracord\Socialite;

use Kyzegs\Laracord\Constants\Permissions;

class PartialGuild
{
    /**
     * @param  int  $id
     * @param  string  $name
     * @param  string|null  $icon
     * @param  bool  $owner
     * @param  int  $permissions
     * @param  array  $features
     * @return void
     */
    public function __construct(
        public int $id,
        public string $name,
        public string|null $icon,
        public bool $owner,
        public int $permissions,
        public array $features,
    ) {
    }

    /**
     * Check if the user has all of the given permissions.
     *
     * @param  int[]  ...$permissions
     * @return bool
     */
    public function hasPermissions(int ...$permissions): bool
    {
        return ! collect($permissions)
            ->map(fn ($permission) => ($this->permissions & $permission) == $permission)
            ->containsStrict(false);
    }

    /**
     * Check if the user has any of the given permissions.
     *
     * @param  int[]  ...$permissions
     * @return bool
     */
    public function hasAnyPermissions(int ...$permissions): bool
    {
        return collect($permissions)
            ->map(fn ($permission) => ($this->permissions & $permission) == $permission)
            ->containsStrict(true);
    }

    /**
     * Return whether or not the user is an administrator.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasPermissions(Permissions::ADMINISTRATOR);
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
        } elseif ($this->isAdmin()) {
            return 'Administrator';
        } else {
            return 'Member';
        }
    }
}
