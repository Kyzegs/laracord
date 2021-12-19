<?php

namespace Kyzegs\Laracord\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Model\Model;
use Kyzegs\Laracord\Client\Http;

class ApplicationCommand extends Model
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
            'guild_id' => 'required|integer',
        ])->validate();

        parent::__construct($attributes);
    }

    /**
     * Helper to get the corresponding route for an API call.
     *
     * @param  string  $method
     * @param  int|null  $guildId
     * @param  bool  $multiple
     * @param  array  ...$values
     * @return string
     */
    protected static function getRoute(string $method, int|null $guildId = null, mixed ...$values): string
    {
        $scope = $guildId ? 'GUILD' : 'GLOBAL';
        $constant = 'Kyzegs\Laracord\Constants\Routes::%s_%s_APPLICATION_COMMAND';

        if ($method === 'GET') {
            $constant = $constant.'S';
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
     * @param  int|null  $guildId
     * @return \Illuminate\Support\Collection
     */
    public static function get(int|null $guildId = null): Collection
    {
        $route = self::getRoute('GET', $guildId);
        $permissions = ApplicationCommandPermission::get($guildId);

        return Http::get($route)
            ->throw()
            ->collect()
            ->map(fn (array $data) => new self($data))
            ->each(function (ApplicationCommand $applicationCommand) use ($permissions) {
                $applicationCommand->permissions = $permissions->firstWhere('id', $applicationCommand->id);
            });
    }

    /**
     * Send an HTTP POST request to Discord to make a new application command.
     *
     * @param  int|null  $guildId
     * @param  array  $data
     * @return \Kyzegs\Laracord\Models\ApplicationCommand
     */
    public static function create(int|null $guildId = null, array $data): self
    {
        $route = self::getRoute('CREATE', $guildId);

        return new self(Http::post($route, $data)->throw()->json());
    }

    /**
     * Send an HTTP PATCH request to Discord with the given data.
     *
     * @param  int|null  $guildId
     * @param  int  $id
     * @param  array  $data
     * @return \Kyzegs\Laracord\Models\ApplicationCommand
     */
    public static function update(int|null $guildId = null, int $id, array $data): self
    {
        $route = self::getRoute('EDIT', $guildId, $id);

        return new self(Http::patch($route, $data)->throw()->json());
    }

    /**
     * Send an HTTP POST or PATCH request to Discord with the current data.
     *
     * @return \Kyzegs\Laracord\Models\ApplicationCommand
     */
    public function save(): self
    {
        $applicationCommand = is_null($this->id)
            ? self::create($this->guild_id, $this->attributes)
            : self::update($this->guild_id, $this->id, $this->attributes);

        return $this->fill($applicationCommand->toArray())->fill($this->permissions()->toArray());
    }

    /**
     * Send an HTTP DELETE request to Discord to delete a given application command.
     *
     * @param  int|null  $guildId
     * @param  int  $id
     * @return void
     */
    public static function delete(int|null $guildId = null, int $id): void
    {
        $route = self::getRoute('DELETE', $guildId, $id);

        Http::delete($route);
    }

    /**
     * Send an HTTP DELETE request to Discord to delete the current application command.
     *
     * @return void
     */
    public function destroy(): void
    {
        self::delete($this->guild_id, $this->id);
    }

    /**
     * Get the permissions for the application command. It'll default to a new permission instance if none exists yet.
     *
     * @return \Kyzegs\Laracord\Models\ApplicationCommandPermission
     */
    public function permissions(): ApplicationCommandPermission
    {
        if (empty($this->permissions) || is_array($this->permissions)) {
            $this->permissions = new ApplicationCommandPermission([
                'id' => $this->id,
                'guild_id' => $this->guild_id,
            ]);
        }

        return $this->permissions;
    }
}
