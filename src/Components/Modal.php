<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/** @implements Arrayable<string, mixed> */
final class Modal implements Arrayable, JsonSerializable
{
    /** @var list<Component> */
    private array $components = [];

    private function __construct(
        private readonly string $customId,
        private readonly string $title,
    ) {
        if ($customId === '' || mb_strlen($customId) > 100) {
            throw new \InvalidArgumentException('Modal custom_id must contain between 1 and 100 characters.');
        }

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

    public function customId(): string
    {
        return $this->customId;
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
        $ids = $customIds = [];
        $this->validateUniqueIdentifiers($components, $ids, $customIds);

        return [
            'custom_id' => $this->customId,
            'title' => $this->title,
            'components' => $components,
        ];
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @param  array<array-key, mixed>  $value
     * @param  array<int, true>  $ids
     * @param  array<string, true>  $customIds
     */
    private function validateUniqueIdentifiers(array $value, array &$ids, array &$customIds): void
    {
        if (isset($value['id']) && is_int($value['id']) && $value['id'] !== 0) {
            if (isset($ids[$value['id']])) {
                throw new \InvalidArgumentException('Component ids must be unique within a modal.');
            }

            $ids[$value['id']] = true;
        }

        if (isset($value['custom_id']) && is_string($value['custom_id'])) {
            if (isset($customIds[$value['custom_id']])) {
                throw new \InvalidArgumentException('Component custom_ids must be unique within a modal.');
            }

            $customIds[$value['custom_id']] = true;
        }

        foreach ($value as $child) {
            if (is_array($child)) {
                $this->validateUniqueIdentifiers($child, $ids, $customIds);
            }
        }
    }
}
