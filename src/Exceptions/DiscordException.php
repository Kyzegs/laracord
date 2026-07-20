<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Exceptions;

use RuntimeException;

class DiscordException extends RuntimeException
{
    /** @return array{type:class-string<static>} */
    public function formatForTelescope(): array
    {
        return ['type' => static::class];
    }
}
