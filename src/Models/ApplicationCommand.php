<?php

namespace Kyzegs\Laracord\Models;

use Illuminate\Support\Collection;
use Kyzegs\Laracord\Constants\Route;
use Kyzegs\Laracord\Facades\ApplicationCommandPermissions;
use Kyzegs\Laracord\Facades\Http;

class ApplicationCommand extends Model
{
    /**
     * Helper to get the corresponding route for an API call.
     *
     * @param  string  $method
     * @param  int|null  $guildId
     * @param  array  ...$values
     * @return string
     */
    private function getRoute(string $method, ?int $guildId, mixed ...$values): string
    {
        $route = match (true) {
            in_array($method, ['GET', 'POST', 'PUT']) && isset($guildId) => Route::GUILD_APPLICATION_COMMANDS,
            in_array($method, ['GET', 'POST', 'PUT']) && is_null($guildId) => Route::GLOBAL_APPLICATION_COMMANDS,
            isset($guildId) => Route::GUILD_APPLICATION_COMMAND,
            is_null($guildId) => Route::GLOBAL_APPLICATION_COMMAND,
        };

        return sprintf($route->value, config('laracord.client_id'), ...array_filter([$guildId, ...$values]));
    }

    /**
     * Get the first related model record matching the attributes or instantiate it.
     *
     * @param  int|null  $guildId
     * @param  array  $attributes
     * @param  array  $values
     * @return static
     */
    public function firstOrNew(array $attributes = [], array $values = [], ?int $guildId = null): static
    {
        return $this->get()->where($attributes)->first() ?? $this->newInstance(['guild_id' => $guildId, ...array_merge($attributes, $values)]);
    }

    /**
     * Get the first related record matching the attributes or create it.
     *
     * @param  int|null  $guildId
     * @param  array  $attributes
     * @param  array  $values
     * @return static
     */
    public function firstOrCreate(array $attributes = [], array $values = [], ?int $guildId = null): static
    {
        return $this->get()->where($attributes)->first() ?? $this->create(array_merge($attributes, $values), $guildId);
    }

    /**
     * Create or update a related record matching the attributes, and fill it with values.
     *
     * @param  int|null  $guildId
     * @param  array  $attributes
     * @param  array  $values
     * @return static
     */
    public function updateOrCreate(array $attributes, array $values = [], ?int $guildId = null): static
    {
        return $this->firstOrNew($attributes, [], $guildId)->fill($values)->save();
    }

    /**
     * Send an HTTP GET request to retrieve data from Discord.
     *
     * @param  int|null  $guildId
     * @return Collection<ApplicationCommand>
     */
    public function get(?int $guildId = null): Collection
    {
        return Http::get($this->getRoute('GET', $guildId))->collect()->mapInto(self::class);
    }

    /**
     * @param  array  $applicationCommands
     * @param  int|null  $guildId
     * @return Collection<ApplicationCommand>
     */
    public function bulk(array $applicationCommands, ?int $guildId = null): Collection
    {
        return Http::put($this->getRoute('PUT', $guildId), $applicationCommands)->collect()->mapInto(self::class);
    }

    /**
     * Send an HTTP POST request to Discord to make a new application command.
     *
     * @param  int|null  $guildId
     * @param  array  $attributes
     * @return ApplicationCommand
     */
    public function create(array $attributes, ?int $guildId = null): static
    {
        return $this->newInstance(Http::post($this->getRoute('POST', $guildId), $attributes)->json());
    }

    /**
     * Send an HTTP PATCH request to Discord with the given data.
     *
     * @param  int|null  $guildId
     * @param  int  $id
     * @param  array  $attributes
     * @return ApplicationCommand
     */
    public function update(array $attributes, int $applicationCommandId, ?int $guildId = null): static
    {
        return $this->newInstance(Http::patch($this->getRoute('PATCH', $guildId, $applicationCommandId), $attributes)->json());
    }

    /**
     * Send an HTTP POST or PATCH request to Discord with the current data.
     *
     * @return ApplicationCommand
     */
    public function save(): static
    {
        if ($this->isClean()) {
            return $this;
        }

        $applicationCommand = is_null($this->id)
            ? self::create($this->attributes, $this->guild_id)
            : self::update($this->attributes, $this->id, $this->guild_id);

        return $this->fill($applicationCommand->toArray());
    }

    /**
     * Send an HTTP DELETE request to Discord to delete a given application command.
     *
     * @param  int|null  $guildId
     * @param  int  $id
     * @return void
     */
    public function delete(int $applicationCommandId, ?int $guildId = null): void
    {
        Http::delete($this->getRoute('DELETE', $guildId, $applicationCommandId));
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
     * @return Collection
     */
    public function permissions(): Collection
    {
        return ApplicationCommandPermissions::get($this->guild_id, $this->id);
    }
}
