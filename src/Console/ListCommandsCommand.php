<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Console;

use Illuminate\Console\Command;
use Kyzegs\Laracord\Console\Concerns\ResolvesApplicationId;
use Kyzegs\Laracord\Facades\Laracord;

final class ListCommandsCommand extends Command
{
    use ResolvesApplicationId;

    protected $signature = "laracord:commands:list {--guild= : List a single guild's commands instead of global ones}";

    protected $description = 'List the Discord application commands currently registered';

    public function handle(): int
    {
        $applicationId = $this->applicationId();
        if ($applicationId === null) {
            return self::FAILURE;
        }

        $guild = $this->option('guild');

        $response = is_string($guild) && $guild !== ''
            ? Laracord::bot()->commands()->listGuildCommands(['application_id' => $applicationId, 'guild_id' => $guild])
            : Laracord::bot()->commands()->listGlobalCommands(['application_id' => $applicationId]);

        /** @var array<int, array<string, mixed>> $commands */
        $commands = (array) $response->json();
        if ($commands === []) {
            $this->info('No commands registered.');

            return self::SUCCESS;
        }

        $this->table(['ID', 'Name', 'Description'], array_map(static fn (array $command): array => [
            (string) ($command['id'] ?? ''),
            (string) ($command['name'] ?? ''),
            (string) ($command['description'] ?? ''),
        ], $commands));

        return self::SUCCESS;
    }
}
