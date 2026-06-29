<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Enums\ComponentType;
use Kyzegs\Laracord\Components\Enums\TextInputStyle;

final class TextInput implements Component
{
    private ?int $minLength = null;

    private ?int $maxLength = null;

    private ?bool $required = null;

    private ?string $value = null;

    private ?string $placeholder = null;

    private function __construct(
        private readonly string $customId,
        private readonly string $label,
        private readonly TextInputStyle $style,
    ) {
        if (mb_strlen($customId) > 100) {
            throw new \InvalidArgumentException('Text input custom_id cannot exceed 100 characters.');
        }

        if (mb_strlen($label) > 45) {
            throw new \InvalidArgumentException('Text input label cannot exceed 45 characters.');
        }
    }

    public static function short(string $customId, string $label): self
    {
        return new self($customId, $label, TextInputStyle::SHORT);
    }

    public static function paragraph(string $customId, string $label): self
    {
        return new self($customId, $label, TextInputStyle::PARAGRAPH);
    }

    public function minLength(int $minLength): self
    {
        $this->minLength = $minLength;

        return $this;
    }

    public function maxLength(int $maxLength): self
    {
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
        return array_filter([
            'type' => ComponentType::TEXT_INPUT->value,
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
