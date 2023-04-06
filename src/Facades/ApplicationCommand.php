<?php

namespace Kyzegs\Laracord\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Kyzegs\Laracord\Models\ApplicationCommand newInstance($attributes = [])
 * @method static \Kyzegs\Laracord\Models\ApplicationCommand firstOrNew(array $attributes = [], array $values = [], ?int $guildId = null)
 * @method static \Kyzegs\Laracord\Models\ApplicationCommand firstOrCreate(array $attributes = [], array $values = [], ?int $guildId = null)
 * @method static \Kyzegs\Laracord\Models\ApplicationCommand updateOrCreate(array $attributes, array $values = [], ?int $guildId = null)
 * @method static Collection<\Kyzegs\Laracord\Models\ApplicationCommand> get(?int $guildId = null)
 * @method static Collection<\Kyzegs\Laracord\Models\ApplicationCommand> bulk(array $applicationCommands, ?int $guildId = null)
 * @method static \Kyzegs\Laracord\Models\ApplicationCommand create(array $attributes, ?int $guildId = null)
 * @method static \Kyzegs\Laracord\Models\ApplicationCommand update(array $attributes, int $applicationCommandId, ?int $guildId = null)
 * @method static \Kyzegs\Laracord\Models\ApplicationCommand delete(int $applicationCommandId, ?int $guildId = null)
 *
 * @see \Kyzegs\Laracord\Models\ApplicationCommand
 */
class ApplicationCommand extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return class-string
     */
    protected static function getFacadeAccessor(): string
    {
        return \Kyzegs\Laracord\Models\ApplicationCommand::class;
    }
}
