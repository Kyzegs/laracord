<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Concerns\HasId;
use Kyzegs\Laracord\Components\Enums\ComponentType;
use Kyzegs\Laracord\Components\Enums\TextInputStyle;

final class TextInput implements Component
{
    use HasId;

    private ?int $minLength = null;

    private ?int $maxLength = null;

    private ?bool $required = null;

    private ?string $value = null;

    private ?string $placeholder = null;

    private function __construct(
        private readonly string $customId,
        private readonly ?string $label,
        private readonly TextInputStyle $style,
    ) {
        if ($customId === '' || mb_strlen($customId) > 100) {
            throw new \InvalidArgumentException('Text input custom_id must contain between 1 and 100 characters.');
        }

        if ($label !== null && ($label === '' || mb_strlen($label) > 45)) {
            throw new \InvalidArgumentException('Text input label cannot exceed 45 characters.');
        }
    }

    public static function short(string $customId, ?string $label = null): self
    {
        return new self($customId, $label, TextInputStyle::SHORT);
    }

    public static function paragraph(string $customId, ?string $label = null): self
    {
        return new self($customId, $label, TextInputStyle::PARAGRAPH);
    }

    public function minLength(int $minLength): self
    {
        if ($minLength < 0 || $minLength > 4000) {
            throw new \InvalidArgumentException('Text input min_length must be between 0 and 4000.');
        }

        $this->minLength = $minLength;

        return $this;
    }

    public function maxLength(int $maxLength): self
    {
        if ($maxLength < 1 || $maxLength > 4000) {
            throw new \InvalidArgumentException('Text input max_length must be between 1 and 4000.');
        }

        $this->maxLength = $maxLength;

        return $this;
    }

    public function required(bool $required = true): self
    {
        $this->required = $required;

        return $this;
    }

    public function value(string $value): self
    {
        if (mb_strlen($value) > 4000) {
            throw new \InvalidArgumentException('Text input value cannot exceed 4000 characters.');
        }

        $this->value = $value;

        return $this;
    }

    public function placeholder(string $placeholder): self
    {
        if (mb_strlen($placeholder) > 100) {
            throw new \InvalidArgumentException('Text input placeholder cannot exceed 100 characters.');
        }

        $this->placeholder = $placeholder;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        if ($this->minLength !== null && $this->maxLength !== null && $this->minLength > $this->maxLength) {
            throw new \InvalidArgumentException('Text input min_length cannot exceed max_length.');
        }

        return array_filter([
            'type' => ComponentType::TEXT_INPUT->value,
            'id' => $this->id,
            'custom_id' => $this->customId,
            'style' => $this->style->value,
            'label' => $this->label,
            'min_length' => $this->minLength,
            'max_length' => $this->maxLength,
            'required' => $this->required,
            'value' => $this->value,
            'placeholder' => $this->placeholder,
        ], static fn (mixed $value): bool => $value !== null);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
