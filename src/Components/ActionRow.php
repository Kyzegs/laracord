<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Concerns\HasId;
use Kyzegs\Laracord\Components\Enums\ComponentType;

final class ActionRow implements Component
{
    use HasId;

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
        } elseif (count($this->components) !== 1 || (! $this->components[0] instanceof SelectMenu && ! $this->components[0] instanceof TextInput)) {
            throw new \InvalidArgumentException('An action row must contain buttons, one select menu, or one text input.');
        }

        return [
            'type' => ComponentType::ACTION_ROW->value,
            ...($this->id === null ? [] : ['id' => $this->id]),
            'components' => array_map(static fn (Component $component): array => $component->toArray(), $this->components),
        ];
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
