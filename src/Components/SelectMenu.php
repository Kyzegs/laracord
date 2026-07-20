<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Concerns\HasId;
use Kyzegs\Laracord\Components\Enums\ComponentType;

abstract class SelectMenu implements Component
{
    use HasId;

    protected ?string $placeholder = null;

    protected ?int $minValues = null;

    protected ?int $maxValues = null;

    protected ?bool $disabled = null;

    protected ?bool $required = null;

    /** @var list<array{id: string, type: string}> */
    protected array $defaultValues = [];

    final public function __construct(protected readonly string $customId)
    {
        if ($customId === '' || mb_strlen($customId) > 100) {
            throw new \InvalidArgumentException('Select menu custom_id must contain between 1 and 100 characters.');
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
        if ($minValues < 0 || $minValues > 25) {
            throw new \InvalidArgumentException('Select menu min_values must be between 0 and 25.');
        }

        $this->minValues = $minValues;

        return $this;
    }

    public function maxValues(int $maxValues): static
    {
        if ($maxValues < 1 || $maxValues > 25) {
            throw new \InvalidArgumentException('Select menu max_values must be between 1 and 25.');
        }

        $this->maxValues = $maxValues;

        return $this;
    }

    public function disabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function required(bool $required = true): static
    {
        $this->required = $required;

        return $this;
    }

    protected function addDefaultValue(string $id, string $type): static
    {
        if ($id === '') {
            throw new \InvalidArgumentException('Select menu default value id cannot be empty.');
        }

        if (count($this->defaultValues) >= 25) {
            throw new \InvalidArgumentException('A select menu supports at most 25 default values.');
        }

        $this->defaultValues[] = ['id' => $id, 'type' => $type];

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
        if ($this->minValues !== null && $this->maxValues !== null && $this->minValues > $this->maxValues) {
            throw new \InvalidArgumentException('Select menu min_values cannot exceed max_values.');
        }

        $defaultCount = count($this->defaultValues);
        if ($this->maxValues !== null && $defaultCount > $this->maxValues) {
            throw new \InvalidArgumentException('Select menu default value count cannot exceed max_values.');
        }

        if ($defaultCount > 0 && $this->minValues !== null && $defaultCount < $this->minValues) {
            throw new \InvalidArgumentException('Select menu default value count cannot be less than min_values.');
        }

        return array_filter([
            'type' => $this->type()->value,
            'id' => $this->id,
            'custom_id' => $this->customId,
            'placeholder' => $this->placeholder,
            'min_values' => $this->minValues,
            'max_values' => $this->maxValues,
            'disabled' => $this->disabled,
            'required' => $this->required,
            'default_values' => $this->defaultValues ?: null,
            ...$this->extra(),
        ], static fn (mixed $value): bool => $value !== null);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
