<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Concerns\HasId;
use Kyzegs\Laracord\Components\Enums\ComponentType;

final class Section implements Component
{
    use HasId;

    /** @var list<Component> */
    private array $components;

    private ?Component $accessory = null;

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
        if (count($this->components) >= 3) {
            throw new \InvalidArgumentException('A section supports at most 3 child components.');
        }

        $this->components[] = $component;

        return $this;
    }

    public function accessory(Component $accessory): self
    {
        $this->accessory = $accessory;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        if ($this->components === [] || count($this->components) > 3) {
            throw new \InvalidArgumentException('A section requires between 1 and 3 child components.');
        }

        if (array_filter($this->components, static fn (Component $component): bool => ! $component instanceof TextDisplay) !== []) {
            throw new \InvalidArgumentException('A section may only contain text displays.');
        }

        if (! $this->accessory instanceof Button && ! $this->accessory instanceof Thumbnail) {
            throw new \InvalidArgumentException('A section requires a button or thumbnail accessory.');
        }

        return array_filter([
            'type' => ComponentType::SECTION->value,
            'id' => $this->id,
            'components' => array_map(static fn (Component $component): array => $component->toArray(), $this->components),
            'accessory' => $this->accessory->toArray(),
        ], static fn (mixed $value): bool => $value !== null);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
