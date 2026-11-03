<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Kyzegs\Laracord\Components\Concerns\BuildsPayload;
use Kyzegs\Laracord\Components\Concerns\ValidatesIdentifiers;

/** @implements Arrayable<string, mixed> */
final class Modal implements Arrayable, JsonSerializable
{
    use BuildsPayload;
    use ValidatesIdentifiers;

    /** @var list<Component> */
    private array $components = [];

    private function __construct(
        private readonly string $customId,
        private readonly string $title,
    ) {
        self::assertCustomId($customId, 'Modal');

        if ($title === '' || mb_strlen($title) > 45) {
            throw new \InvalidArgumentException('Modal title must contain between 1 and 45 characters.');
        }
    }

    public static function make(string $customId, string $title): self
    {
        return new self($customId, $title);
    }

    public function text(TextInput $input): self
    {
        return $this->add(new ActionRow($input));
    }

    public function label(string $label, Component $component, ?string $description = null): self
    {
        $field = Label::make($label, $component);
        if ($description !== null) {
            $field->description($description);
        }

        return $this->add($field);
    }

    public function add(Component $component): self
    {
        if (count($this->components) >= 40) {
            throw new \InvalidArgumentException('A modal supports at most 40 top-level components.');
        }

        $this->components[] = $component;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        if ($this->components === []) {
            throw new \InvalidArgumentException('A modal requires at least one component.');
        }

        $allowed = [ActionRow::class, Label::class, TextDisplay::class];
        if (array_filter($this->components, static fn (Component $component): bool => ! in_array($component::class, $allowed, true)) !== []) {
            throw new \InvalidArgumentException('Modal contains an unsupported top-level component.');
        }

        $components = array_map(static fn (Component $component): array => $component->toArray(), $this->components);
        $this->validateUniqueIdentifiers($components, 'modal');

        return [
            'custom_id' => $this->customId,
            'title' => $this->title,
            'components' => $components,
        ];
    }
}
