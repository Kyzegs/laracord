<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Console;

use Illuminate\Console\Command;
use Kyzegs\Laracord\Console\Concerns\ResolvesApplicationId;
use Kyzegs\Laracord\Facades\Laracord;

final class SyncCommandsCommand extends Command
{
    use ResolvesApplicationId;

    protected $signature = 'laracord:commands:sync {--guild= : Sync to a single guild instead of globally}';

    protected $description = 'Bulk overwrite Discord application commands from config (laracord.commands)';

    public function handle(): int
    {
        $applicationId = $this->applicationId();
        if ($applicationId === null) {
            return self::FAILURE;
        }

        /** @var array<string, mixed> $commands a JSON array of command definitions */
        $commands = (array) config('laracord.commands', []);
        $guild = $this->option('guild');

        if (is_string($guild) && $guild !== '') {
            Laracord::bot()->commands()->bulkOverwriteGuildCommands(
                ['application_id' => $applicationId, 'guild_id' => $guild],
                $commands,
            );

            $this->info(sprintf('Synced %d command(s) to guild %s.', count($commands), $guild));

            return self::SUCCESS;
        }

        Laracord::bot()->commands()->bulkOverwriteGlobalCommands(['application_id' => $applicationId], $commands);

        $this->info(sprintf('Synced %d command(s) globally.', count($commands)));

        return self::SUCCESS;
    }
}
