<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Concerns\HasId;
use Kyzegs\Laracord\Components\Enums\ComponentType;

final class FileUpload implements Component
{
    use HasId;

    private ?int $minValues = null;

    private ?int $maxValues = null;

    private ?bool $required = null;

    private function __construct(private readonly string $customId)
    {
        if ($customId === '' || mb_strlen($customId) > 100) {
            throw new \InvalidArgumentException('File upload custom_id must contain between 1 and 100 characters.');
        }
    }

    public static function make(string $customId): self
    {
        return new self($customId);
    }

    public function minValues(int $minValues): self
    {
        if ($minValues < 0 || $minValues > 10) {
            throw new \InvalidArgumentException('File upload min_values must be between 0 and 10.');
        }

        $this->minValues = $minValues;

        return $this;
    }

    public function maxValues(int $maxValues): self
    {
        if ($maxValues < 1 || $maxValues > 10) {
            throw new \InvalidArgumentException('File upload max_values must be between 1 and 10.');
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
        if ($this->minValues !== null && $this->maxValues !== null && $this->minValues > $this->maxValues) {
            throw new \InvalidArgumentException('File upload min_values cannot exceed max_values.');
        }

        if ($this->minValues === 0 && $this->required !== false) {
            throw new \InvalidArgumentException('File upload min_values may be 0 only when required is false.');
        }

        return array_filter([
            'type' => ComponentType::FILE_UPLOAD->value,
            'id' => $this->id,
            'custom_id' => $this->customId,
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
