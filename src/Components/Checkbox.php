<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Concerns\HasId;
use Kyzegs\Laracord\Components\Enums\ComponentType;

final class Checkbox implements Component
{
    use HasId;

    private ?bool $default = null;

    private function __construct(private readonly string $customId)
    {
        if ($customId === '' || mb_strlen($customId) > 100) {
            throw new \InvalidArgumentException('Checkbox custom_id must contain between 1 and 100 characters.');
        }
    }

    public static function make(string $customId): self
    {
        return new self($customId);
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
            'type' => ComponentType::CHECKBOX->value,
            'id' => $this->id,
            'custom_id' => $this->customId,
            'default' => $this->default,
        ], static fn (mixed $value): bool => $value !== null);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
