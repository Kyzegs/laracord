<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Concerns\HasId;
use Kyzegs\Laracord\Components\Enums\ComponentType;

final class Container implements Component
{
    use HasId;

    /** @var list<Component> */
    private array $components;

    private ?int $accentColor = null;

    private ?bool $spoiler = null;

    private function __construct(Component ...$components)
    {
        $this->components = array_values($components);
    }

    public static function make(Component ...$components): self
    {
        return new self(...$components);
    }

    public function add(Component $component): self
    {
        if (count($this->components) >= 40) {
            throw new \InvalidArgumentException('A container supports at most 40 child components.');
        }

        $this->components[] = $component;

        return $this;
    }

    public function accentColor(int $color): self
    {
        if ($color < 0 || $color > 0xFFFFFF) {
            throw new \InvalidArgumentException('Container accent color must be between 0x000000 and 0xFFFFFF.');
        }

        $this->accentColor = $color;

        return $this;
    }

    public function spoiler(bool $spoiler = true): self
    {
        $this->spoiler = $spoiler;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        if ($this->components === [] || count($this->components) > 40) {
            throw new \InvalidArgumentException('A container requires between 1 and 40 child components.');
        }

        $allowed = [ActionRow::class, File::class, MediaGallery::class, Section::class, Separator::class, TextDisplay::class];
        if (array_filter($this->components, static fn (Component $component): bool => ! in_array($component::class, $allowed, true)) !== []) {
            throw new \InvalidArgumentException('Container contains an unsupported child component.');
        }

        return array_filter([
            'type' => ComponentType::CONTAINER->value,
            'id' => $this->id,
            'accent_color' => $this->accentColor,
            'components' => array_map(static fn (Component $component): array => $component->toArray(), $this->components),
            'spoiler' => $this->spoiler,
        ], static fn (mixed $value): bool => $value !== null);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
