<?php

namespace Kyzegs\Laracord\Socialite;

use Kyzegs\Laracord\Constants\Permissions;

class PartialGuild
{
    /**
     * @return void
     */
    public function __construct(
        public int $id,
        public string $name,
        public ?string $icon,
        public bool $owner,
        public int $permissions,
        public array $features,
    ) {
    }

    /**
     * Check if the user has all of the given permissions.
     *
     * @param  int[]  ...$permissions
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
     */
    public function hasAnyPermissions(int ...$permissions): bool
    {
        return collect($permissions)
            ->map(fn ($permission) => ($this->permissions & $permission) == $permission)
            ->containsStrict(true);
    }

    /**
     * Return whether or not the user is an administrator.
     */
    public function isAdmin(): bool
    {
        return $this->hasPermissions(Permissions::ADMINISTRATOR);
    }

    /**
     * Determine what role a user has within the given guild.
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
