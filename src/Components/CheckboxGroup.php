<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Concerns\HasId;
use Kyzegs\Laracord\Components\Enums\ComponentType;

final class CheckboxGroup implements Component
{
    use HasId;

    /** @var list<SelectOption> */
    private array $options = [];

    private ?int $minValues = null;

    private ?int $maxValues = null;

    private ?bool $required = null;

    private function __construct(private readonly string $customId)
    {
        if ($customId === '' || mb_strlen($customId) > 100) {
            throw new \InvalidArgumentException('Checkbox group custom_id must contain between 1 and 100 characters.');
        }
    }

    public static function make(string $customId): self
    {
        return new self($customId);
    }

    public function option(SelectOption $option): self
    {
        if (count($this->options) >= 10) {
            throw new \InvalidArgumentException('A checkbox group supports at most 10 options.');
        }

        $this->options[] = $option;

        return $this;
    }

    public function options(SelectOption ...$options): self
    {
        foreach ($options as $option) {
            $this->option($option);
        }

        return $this;
    }

    public function minValues(int $minValues): self
    {
        if ($minValues < 0 || $minValues > 10) {
            throw new \InvalidArgumentException('Checkbox group min_values must be between 0 and 10.');
        }

        $this->minValues = $minValues;

        return $this;
    }

    public function maxValues(int $maxValues): self
    {
        if ($maxValues < 1 || $maxValues > 10) {
            throw new \InvalidArgumentException('Checkbox group max_values must be between 1 and 10.');
        }

        $this->maxValues = $maxValues;

        return $this;
    }

    public function required(bool $required = true): self
    {
        $this->required = $required;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        if ($this->options === []) {
            throw new \InvalidArgumentException('A checkbox group requires between 1 and 10 options.');
        }

        if ($this->minValues !== null && $this->maxValues !== null && $this->minValues > $this->maxValues) {
            throw new \InvalidArgumentException('Checkbox group min_values cannot exceed max_values.');
        }

        if ($this->maxValues !== null && $this->maxValues > count($this->options)) {
            throw new \InvalidArgumentException('Checkbox group max_values cannot exceed its option count.');
        }

        if ($this->minValues !== null && $this->minValues > count($this->options)) {
            throw new \InvalidArgumentException('Checkbox group min_values cannot exceed its option count.');
        }

        if ($this->minValues === 0 && $this->required !== false) {
            throw new \InvalidArgumentException('Checkbox group min_values may be 0 only when required is false.');
        }

        return array_filter([
            'type' => ComponentType::CHECKBOX_GROUP->value,
            'id' => $this->id,
            'custom_id' => $this->customId,
            'options' => array_map(static fn (SelectOption $option): array => $option->toArray(), $this->options),
            'min_values' => $this->minValues,
            'max_values' => $this->maxValues,
            'required' => $this->required,
        ], static fn (mixed $value): bool => $value !== null);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
