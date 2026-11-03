<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Console;

use Illuminate\Console\Command;
use Kyzegs\Laracord\Console\Concerns\ResolvesApplicationId;
use Kyzegs\Laracord\Facades\Laracord;

final class ClearCommandsCommand extends Command
{
    use ResolvesApplicationId;

    protected $signature = "laracord:commands:clear
        {--guild= : Clear a single guild's commands instead of global ones}
        {--force : Skip the confirmation}";

    protected $description = 'Remove all registered Discord application commands';

    public function handle(): int
    {
        $applicationId = $this->applicationId();
        if ($applicationId === null) {
            return self::FAILURE;
        }

        $guild = $this->option('guild');
        $scoped = is_string($guild) && $guild !== '';

        if (! $this->option('force') && ! $this->confirm($scoped ? sprintf('Remove all Discord commands for guild %s?', $guild) : 'Remove all global Discord commands?')) {
            return self::FAILURE;
        }

        if ($scoped) {
            Laracord::bot()->commands()->bulkOverwriteGuildCommands(['application_id' => $applicationId, 'guild_id' => $guild], []);

            $this->info(sprintf('Cleared all commands for guild %s.', $guild));

            return self::SUCCESS;
        }

        Laracord::bot()->commands()->bulkOverwriteGlobalCommands(['application_id' => $applicationId], []);

        $this->info('Cleared all global commands.');

        return self::SUCCESS;
    }
}
