<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Concerns\HasId;
use Kyzegs\Laracord\Components\Enums\ComponentType;

final class Separator implements Component
{
    use HasId;

    private ?bool $divider = null;

    private ?int $spacing = null;

    public static function make(): self
    {
        return new self;
    }

    public function divider(bool $divider = true): self
    {
        $this->divider = $divider;

        return $this;
    }

    public function spacing(int $spacing): self
    {
        if (! in_array($spacing, [1, 2], true)) {
            throw new \InvalidArgumentException('Separator spacing must be 1 (small) or 2 (large).');
        }

        $this->spacing = $spacing;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'type' => ComponentType::SEPARATOR->value,
            'id' => $this->id,
            'divider' => $this->divider,
            'spacing' => $this->spacing,
        ], static fn (mixed $value): bool => $value !== null);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
