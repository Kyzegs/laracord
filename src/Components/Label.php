<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Concerns\HasId;
use Kyzegs\Laracord\Components\Enums\ComponentType;

final class Label implements Component
{
    use HasId;

    private ?string $description = null;

    private function __construct(
        private readonly string $label,
        private readonly Component $component,
    ) {
        if ($label === '' || mb_strlen($label) > 45) {
            throw new \InvalidArgumentException('Label text must contain between 1 and 45 characters.');
        }
    }

    public static function make(string $label, Component $component): self
    {
        return new self($label, $component);
    }

    public function description(string $description): self
    {
        if ($description === '' || mb_strlen($description) > 100) {
            throw new \InvalidArgumentException('Label description must contain between 1 and 100 characters.');
        }

        $this->description = $description;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $allowed = [
            TextInput::class,
            StringSelect::class,
            UserSelect::class,
            RoleSelect::class,
            MentionableSelect::class,
            ChannelSelect::class,
            FileUpload::class,
            RadioGroup::class,
            CheckboxGroup::class,
            Checkbox::class,
        ];

        if (! in_array($this->component::class, $allowed, true)) {
            throw new \InvalidArgumentException('Label contains an unsupported child component.');
        }

        return array_filter([
            'type' => ComponentType::LABEL->value,
            'id' => $this->id,
            'label' => $this->label,
            'description' => $this->description,
            'component' => $this->component->toArray(),
        ], static fn (mixed $value): bool => $value !== null);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
