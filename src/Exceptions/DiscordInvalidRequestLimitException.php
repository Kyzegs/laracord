<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Exceptions;

final class DiscordInvalidRequestLimitException extends DiscordException
{
    public function __construct(public readonly float $retryAfter, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('Discord invalid-request safety budget exhausted; retry in %.2f seconds.', $retryAfter), previous: $previous);
    }
}
