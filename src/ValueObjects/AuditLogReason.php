<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\ValueObjects;

use Stringable;

final readonly class AuditLogReason implements Stringable
{
    public function __construct(public string $value)
    {
        if ($value === '' || mb_strlen($value) > 512) {
            throw new \InvalidArgumentException('Audit log reason must contain 1-512 characters.');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
