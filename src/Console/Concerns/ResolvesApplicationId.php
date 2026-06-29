<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Console\Concerns;

trait ResolvesApplicationId
{
    /** Resolve the configured application id, reporting an error when it is missing. */
    protected function applicationId(): ?string
    {
        $applicationId = config('laracord.application_id');
        if ($applicationId === null || $applicationId === '') {
            $this->error('Set DISCORD_APPLICATION_ID (laracord.application_id) before managing commands.');

            return null;
        }

        return (string) $applicationId;
    }
}
