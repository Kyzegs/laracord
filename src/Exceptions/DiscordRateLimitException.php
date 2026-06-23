<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Exceptions;

final class DiscordRateLimitException extends DiscordException
{
    public function __construct(public readonly float $retryAfter, public readonly bool $global = false, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('Discord rate limit exhausted; retry in %.2f seconds.', $retryAfter), previous: $previous);
    }
}
