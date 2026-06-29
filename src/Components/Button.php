<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Concerns\HasEmoji;
use Kyzegs\Laracord\Components\Enums\ButtonStyle;
use Kyzegs\Laracord\Components\Enums\ComponentType;

final class Button implements Component
{
    use HasEmoji;

    private function __construct(
        private readonly ButtonStyle $buttonStyle,
        private ?string $label = null,
        private ?string $customId = null,
        private ?string $url = null,
        private ?string $skuId = null,
        private bool $disabled = false,
    ) {}

    public static function primary(string $customId, ?string $label = null): self
    {
        return new self(ButtonStyle::PRIMARY, $label, customId: $customId);
    }

    public static function secondary(string $customId, ?string $label = null): self
    {
        return new self(ButtonStyle::SECONDARY, $label, customId: $customId);
    }

    public static function success(string $customId, ?string $label = null): self
    {
        return new self(ButtonStyle::SUCCESS, $label, customId: $customId);
    }

    public static function danger(string $customId, ?string $label = null): self
    {
        return new self(ButtonStyle::DANGER, $label, customId: $customId);
    }

    public static function link(string $url, ?string $label = null): self
    {
        return new self(ButtonStyle::LINK, $label, url: $url);
    }

    public static function premium(string $skuId): self
    {
        return new self(ButtonStyle::PREMIUM, skuId: $skuId);
    }

    public function label(string $label): self
    {
        if (mb_strlen($label) > 80) {
            throw new \InvalidArgumentException('Button label cannot exceed 80 characters.');
        }

        $this->label = $label;

        return $this;
    }

    public function disabled(bool $disabled = true): self
    {
        $this->disabled = $disabled;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        if ($this->customId !== null && mb_strlen($this->customId) > 100) {
            throw new \InvalidArgumentException('Button custom_id cannot exceed 100 characters.');
        }

        return array_filter([
            'type' => ComponentType::BUTTON->value,
            'style' => $this->buttonStyle->value,
            'label' => $this->label,
            'emoji' => $this->emoji,
            'custom_id' => $this->customId,
            'url' => $this->url,
            'sku_id' => $this->skuId,
            'disabled' => $this->disabled ?: null,
        ], static fn (mixed $value): bool => $value !== null);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
