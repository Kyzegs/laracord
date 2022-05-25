<?php

namespace Kyzegs\Laracord\Models;

use Illuminate\Support\Collection;
use Kyzegs\Laracord\Constants\Route;
use Kyzegs\Laracord\Facades\Http;
use Kyzegs\Laracord\Traits\Cacheable;

class ApplicationCommandPermissions extends Model
{
    /**
     * @param  int  $guildId
     * @param  int|null  $commandId
     * @param  mixed  ...$values
     * @return string
     */
    public function getRoute(int $guildId, int|null $commandId): string
    {
        $route = isset($commandId)
            ? Route::APPLICATION_COMMAND_PERMISSIONS
            : Route::GUILD_APPLICATION_COMMAND_PERMISSONS;

        return sprintf($route->value, config('laracord.client_id'), $guildId, ...array_filter([$commandId]));
    }

    /**
     * @param  int  $guildId
     * @param  int|null  $commandId
     * @return Collection
     */
    public function get(int $guildId, int|null $commandId = null): Collection
    {
        return Http::get($this->getRoute($guildId, $commandId))->collect()->mapInto(self::class);
    }

    /**
     * @param  int  $guildId
     * @param  int  $commandId
     * @param  array  $permissions
     * @return ApplicationCommandPermissions
     */
    public function update(int $guildId, int $commandId, array $permissions): static
    {
        // Only update if dirty
        return $this->newInstance(Http::withToken(auth()->user()->access_token)->put($this->getRoute($guildId, $commandId), ['permissions' => $permissions]));
    }
}
