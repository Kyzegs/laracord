<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Enums\ComponentType;

final class ActionRow implements Component
{
    /** @var list<Component> */
    private array $components;

    public function __construct(Component ...$components)
    {
        $this->components = array_values($components);
    }

    public static function make(Component ...$components): self
    {
        return new self(...$components);
    }

    public function add(Component $component): self
    {
        $this->components[] = $component;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        if ($this->components === []) {
            throw new \InvalidArgumentException('An action row requires at least one component.');
        }

        $buttons = array_filter($this->components, static fn (Component $component): bool => $component instanceof Button);
        if (count($buttons) === count($this->components)) {
            if (count($this->components) > 5) {
                throw new \InvalidArgumentException('An action row supports at most 5 buttons.');
            }
        } elseif (count($this->components) > 1) {
            throw new \InvalidArgumentException('An action row with a select menu or text input may not contain other components.');
        }

        return [
            'type' => ComponentType::ACTION_ROW->value,
            'components' => array_map(static fn (Component $component): array => $component->toArray(), $this->components),
        ];
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
