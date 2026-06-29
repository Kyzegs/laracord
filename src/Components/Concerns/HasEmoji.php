<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components\Concerns;

trait HasEmoji
{
    /** @var array<string, mixed>|null */
    private ?array $emoji = null;

    /**
     * Attach an emoji. Pass a unicode glyph as $name, or a custom emoji's name plus its id.
     */
    public function emoji(string $name, ?string $id = null, bool $animated = false): static
    {
        $this->emoji = array_filter([
            'name' => $name,
            'id' => $id,
            'animated' => $animated ?: null,
        ], static fn (mixed $value): bool => $value !== null);

        return $this;
    }
}
