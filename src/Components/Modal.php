<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/** @implements Arrayable<string, mixed> */
final class Modal implements Arrayable, JsonSerializable
{
    /** @var list<ActionRow> */
    private array $components = [];

    private function __construct(
        private readonly string $customId,
        private readonly string $title,
    ) {
        if (mb_strlen($customId) > 100) {
            throw new \InvalidArgumentException('Modal custom_id cannot exceed 100 characters.');
        }

        if (mb_strlen($title) > 45) {
            throw new \InvalidArgumentException('Modal title cannot exceed 45 characters.');
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

    public function add(ActionRow $row): self
    {
        if (count($this->components) >= 5) {
            throw new \InvalidArgumentException('A modal supports at most 5 action rows.');
        }

        $this->components[] = $row;

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

        return [
            'custom_id' => $this->customId,
            'title' => $this->title,
            'components' => array_map(static fn (ActionRow $row): array => $row->toArray(), $this->components),
        ];
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
