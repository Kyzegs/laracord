<?php

namespace Kyzegs\Laracord\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Model\Model;
use Kyzegs\Laracord\Client\Http;
use Kyzegs\Laracord\Enums\ApplicationCommandPermissionType;

class ApplicationCommandPermission extends Model
{
    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        Validator::make($attributes, [
            'id' => 'required|integer',
            'guild_id' => 'required|integer',
        ])->validate();

        if (! array_key_exists('permissions', $attributes)) {
            $attributes['permissions'] = [];
        }

        parent::__construct($attributes);
    }

    /**
     * Helper to get the corresponding route for an API call.
     *
     * @param  string  $method
     * @param  int|null  $guildId
     * @param  array  ...$values
     * @return string
     */
    protected static function getRoute(string $method, int|null $guildId = null, mixed ...$values): string
    {
        $scope = $guildId ? 'GUILD' : 'GLOBAL';
        $constant = 'Kyzegs\Laracord\Constants\Routes::%s_APPLICATION_COMMAND_PERMISSIONS';

        if (count(array_filter($values)) < 1) {
            $constant = 'Kyzegs\Laracord\Constants\Routes::%s_GUILD_APPLICATION_COMMAND_PERMISSIONS';
        }

        $route = constant(sprintf($constant, $method, $scope));

        if (is_null($guildId)) {
            return sprintf($route, config('laracord.client_id'), ...$values);
        } else {
            return sprintf($route, config('laracord.client_id'), $guildId, ...$values);
        }
    }

    /**
     * Send an HTTP GET request to retrieve data from Discord.
     *
     * @param  int  $guildId
     * @return \Illuminate\Support\Collection
     */
    public static function get(int $guildId): Collection
    {
        $route = self::getRoute('GET', $guildId);

        return Http::get($route)
            ->throw()
            ->collect()
            ->map(fn (array $data) => new self($data));
    }

    /**
     * Send an HTTP PUT request to Discord with the given data.
     *
     * @param  int|null  $guildId
     * @param  int  $commandId
     * @param  array  $data
     * @return \Kyzegs\Laracord\Models\ApplicationCommandPermission
     */
    public static function update(int|null $guildId = null, int $commandId, array $data): self
    {
        $route = self::getRoute('EDIT', $guildId, $commandId);

        return new self(Http::put($route, $data)->throw()->json());
    }

    /**
     * Send an HTTP PUT request to Discord with the current data.
     *
     * @return \Kyzegs\Laracord\Models\ApplicationCommandPermission
     */
    public function save(): self
    {
        return $this->fill(self::update($this->guild_id, $this->id, ['permissions' => $this->permissions])->toArray());
    }

    /**
     * Add a new overwrite.
     *
     * @param  int  $id
     * @param  \Kyzegs\Laracord\Enums\ApplicationCommandPermissionType|int  $type
     * @param  bool  $permission
     * @return void
     */
    public function add(int $id, ApplicationCommandPermissionType|int $type, bool $permission): void
    {
        $this->permissions = [...$this->permissions, ['id' => $id, 'type' => $type, 'permission' => $permission]];
    }

    /**
     * Remove overwrites for all the given IDs.
     *
     * @param  int[]  ...$ids
     * @return void
     */
    public function remove(int ...$ids): void
    {
        $this->permissions = array_values(array_filter($this->permissions, fn (array $value) => ! in_array($value['id'], $ids)));
    }

    /**
     * Set the permission overwrites from an array.
     *
     * @param  array  $permissions
     * @return void
     */
    public function set(array $permissions): void
    {
        $this->permissions = $permissions;
    }

    /**
     * Remove all overwrites.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->permissions = [];
    }
}
