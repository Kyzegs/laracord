<?php

namespace Kyzegs\Laracord\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Jenssegers\Model\Model;
use Kyzegs\Laracord\Client\Http;
use Kyzegs\Laracord\Constants\Routes;
use Kyzegs\Laracord\Traits\HasAttributes;

class ApplicationCommand extends Model
{
    use HasAttributes;

    /**
     * Create a new  model instance.
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
        $this->syncOriginal();
    }

    private static function getCacheKey(int $guildId): string
    {
        return sprintf('application-commands-%d', $guildId);
    }

    private static function addToCache(int $guildId, ApplicationCommand $applicationCommand): ApplicationCommand
    {
        if (Cache::has(self::getCacheKey($guildId))) {
            Cache::pull(self::getCacheKey($guildId))
                ->push($applicationCommand)
                ->tap(fn (Collection $commands) => Cache::put(self::getCacheKey($guildId), $commands, 3600));
        }

        return $applicationCommand;
    }

    private static function updateCache(int $guildId, ApplicationCommand $applicationCommand): ApplicationCommand
    {
        if (Cache::has(self::getCacheKey($guildId))) {
            Cache::pull(self::getCacheKey($guildId))
                ->filter(fn (ApplicationCommand $command) => $command->id !== $applicationCommand->id)
                ->push($applicationCommand)
                ->tap(fn (Collection $commands) => Cache::put(self::getCacheKey($guildId), $commands, 3600));
        }

        return $applicationCommand;
    }

    private static function deleteFromCache(int $guildId, int $applicationCommandId): void
    {
        Cache::pull(self::getCacheKey($guildId))
            ->filter(fn (ApplicationCommand $command) => $command->id !== $applicationCommandId)
            ->tap(fn (Collection $commands) => Cache::put(self::getCacheKey($guildId), $commands, 3600));
    }

    /**
     * Helper to get the corresponding route for an API call.
     *
     * @param  int|null  $guildId
     * @param  bool  $multiple
     * @param  array  ...$values
     */
    protected static function getRoute(string $method, int $guildId = null, mixed ...$values): string
    {
        $scope = $guildId ? 'GUILD' : 'GLOBAL';
        $constant = 'Kyzegs\Laracord\Constants\Routes::%s_%s_APPLICATION_COMMAND';

        if ($method === 'GET') {
            $constant = Str::of($constant)->append('S')->toString();
        }

        $route = constant(sprintf($constant, $method, $scope));

        if (is_null($guildId)) {
            return sprintf($route, config('laracord.client_id'), ...$values);
        } else {
            return sprintf($route, config('laracord.client_id'), $guildId, ...$values);
        }
    }

    /**
     * @return ApplicationCommand
     */
    public static function firstOrNew(int $guildId, ?int $applicationCommandId): static
    {
        return self::get($guildId)->firstWhere('id', $applicationCommandId) ?? new self(['guild_id' => $guildId]);
    }

    /**
     * @param  int|null  $guildId
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public static function bulkOverwrite(int $guildId = null, array $applicationCommands): Collection
    {
        return Http::put(sprintf(Routes::BULK_OVERWRITE_GUILD_APPLICATION_COMMANDS, config('laracord.client_id'), $guildId), $applicationCommands)
            ->throw()
            ->collect()
            ->map(fn (array $data) => new self($data))
            ->tap(fn (Collection $applicationCommands) => Cache::put(self::getCacheKey($guildId), $applicationCommands, 3600));
    }

    /**
     * Send an HTTP GET request to retrieve data from Discord.
     *
     * @param  int|null  $guildId
     */
    public static function get(int $guildId = null): Collection
    {
        return Cache::remember(self::getCacheKey($guildId), 3600, function () use ($guildId) {
            return Http::get(self::getRoute('GET', $guildId))
                ->throw()
                ->collect()
                ->map(fn (array $data) => new self($data));
        });
    }

    /**
     * Send an HTTP POST request to Discord to make a new application command.
     *
     * @param  int|null  $guildId
     * @return ApplicationCommand
     */
    public static function create(int $guildId = null, array $data): static
    {
        return self::addToCache($guildId, new self(Http::post(self::getRoute('CREATE', $guildId), $data)->throw()->json()));
    }

    /**
     * Send an HTTP PATCH request to Discord with the given data.
     *
     * @param  int|null  $guildId
     * @return ApplicationCommand
     */
    public static function update(int $guildId = null, int $id, array $data): static
    {
        return self::updateCache($guildId, new self(Http::patch(self::getRoute('EDIT', $guildId, $id), $data)->throw()->json()));
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
            ? self::create($this->guild_id, $this->attributes)
            : self::update($this->guild_id, $this->id, $this->attributes);

        return $this->fill($applicationCommand->toArray());
    }

    /**
     * Send an HTTP DELETE request to Discord to delete a given application command.
     *
     * @param  int|null  $guildId
     */
    public static function delete(int $guildId = null, int $id): void
    {
        Http::delete(self::getRoute('DELETE', $guildId, $id));
        self::deleteFromCache($guildId, $id);
    }

    /**
     * Send an HTTP DELETE request to Discord to delete the current application command.
     */
    public function destroy(): void
    {
        self::delete($this->guild_id, $this->id);
        self::deleteFromCache($this->guild_id, $this->id);
    }
}
