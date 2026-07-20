<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Kyzegs\Laracord\Components\Concerns\HasEmoji;

/** @implements Arrayable<string, mixed> */
final class SelectOption implements Arrayable, JsonSerializable
{
    use HasEmoji;

    private function __construct(
        private readonly string $label,
        private readonly string $value,
        private ?string $description = null,
        private bool $default = false,
    ) {}

    public static function make(string $label, string $value): self
    {
        if ($label === '' || mb_strlen($label) > 100) {
            throw new \InvalidArgumentException('Select option label must contain between 1 and 100 characters.');
        }

        if ($value === '' || mb_strlen($value) > 100) {
            throw new \InvalidArgumentException('Select option value must contain between 1 and 100 characters.');
        }

        return new self($label, $value);
    }

    public function description(string $description): self
    {
        if (mb_strlen($description) > 100) {
            throw new \InvalidArgumentException('Select option description cannot exceed 100 characters.');
        }

        $this->description = $description;

        return $this;
    }

    public function default(bool $default = true): self
    {
        $this->default = $default;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter([
            'label' => $this->label,
            'value' => $this->value,
            'description' => $this->description,
            'emoji' => $this->emoji,
            'default' => $this->default ?: null,
        ], static fn (mixed $value): bool => $value !== null);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
