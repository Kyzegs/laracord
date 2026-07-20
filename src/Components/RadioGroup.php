<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Concerns\HasId;
use Kyzegs\Laracord\Components\Enums\ComponentType;

final class RadioGroup implements Component
{
    use HasId;

    /** @var list<SelectOption> */
    private array $options = [];

    private ?bool $required = null;

    private function __construct(private readonly string $customId)
    {
        if ($customId === '' || mb_strlen($customId) > 100) {
            throw new \InvalidArgumentException('Radio group custom_id must contain between 1 and 100 characters.');
        }
    }

    public static function make(string $customId): self
    {
        return new self($customId);
    }

    public function option(SelectOption $option): self
    {
        if (count($this->options) >= 10) {
            throw new \InvalidArgumentException('A radio group supports at most 10 options.');
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

    public function required(bool $required = true): self
    {
        $this->required = $required;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        if (count($this->options) < 2) {
            throw new \InvalidArgumentException('A radio group requires between 2 and 10 options.');
        }

        $options = array_map(static fn (SelectOption $option): array => $option->toArray(), $this->options);
        if (count(array_filter($options, static fn (array $option): bool => ($option['default'] ?? false) === true)) > 1) {
            throw new \InvalidArgumentException('A radio group supports at most one default option.');
        }

        return array_filter([
            'type' => ComponentType::RADIO_GROUP->value,
            'id' => $this->id,
            'custom_id' => $this->customId,
            'options' => $options,
            'required' => $this->required,
        ], static fn (mixed $value): bool => $value !== null);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
