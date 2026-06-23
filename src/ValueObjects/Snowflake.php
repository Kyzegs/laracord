<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\ValueObjects;

use Stringable;

final readonly class Snowflake implements Stringable
{
    public function __construct(public string $value)
    {
        if (preg_match('/^\d{1,20}$/', $value) !== 1) {
            throw new \InvalidArgumentException('Discord snowflake must be a decimal string.');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
