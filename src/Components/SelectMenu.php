<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Enums\ComponentType;

abstract class SelectMenu implements Component
{
    protected ?string $placeholder = null;

    protected ?int $minValues = null;

    protected ?int $maxValues = null;

    protected bool $disabled = false;

    final public function __construct(protected readonly string $customId)
    {
        if (mb_strlen($customId) > 100) {
            throw new \InvalidArgumentException('Select menu custom_id cannot exceed 100 characters.');
        }
    }

    public static function make(string $customId): static
    {
        return new static($customId);
    }

    public function placeholder(string $placeholder): static
    {
        if (mb_strlen($placeholder) > 150) {
            throw new \InvalidArgumentException('Select menu placeholder cannot exceed 150 characters.');
        }

        $this->placeholder = $placeholder;

        return $this;
    }

    public function minValues(int $minValues): static
    {
        $this->minValues = $minValues;

        return $this;
    }

    public function maxValues(int $maxValues): static
    {
        $this->maxValues = $maxValues;

        return $this;
    }

    public function disabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;

        return $this;
    }

    abstract protected function type(): ComponentType;

    /** @return array<string, mixed> */
    protected function extra(): array
    {
        return [];
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'type' => $this->type()->value,
            'custom_id' => $this->customId,
            'placeholder' => $this->placeholder,
            'min_values' => $this->minValues,
            'max_values' => $this->maxValues,
            'disabled' => $this->disabled ?: null,
            ...$this->extra(),
        ], static fn (mixed $value): bool => $value !== null);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
