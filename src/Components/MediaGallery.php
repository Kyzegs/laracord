<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Concerns\HasId;
use Kyzegs\Laracord\Components\Enums\ComponentType;

final class MediaGallery implements Component
{
    use HasId;

    /** @var list<MediaGalleryItem> */
    private array $items;

    private function __construct(MediaGalleryItem ...$items)
    {
        $this->items = array_values($items);
    }

    public static function make(MediaGalleryItem ...$items): self
    {
        return new self(...$items);
    }

    public function item(MediaGalleryItem $item): self
    {
        if (count($this->items) >= 10) {
            throw new \InvalidArgumentException('A media gallery supports at most 10 items.');
        }

        $this->items[] = $item;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        if ($this->items === [] || count($this->items) > 10) {
            throw new \InvalidArgumentException('A media gallery requires between 1 and 10 items.');
        }

        return array_filter([
            'type' => ComponentType::MEDIA_GALLERY->value,
            'id' => $this->id,
            'items' => array_map(static fn (MediaGalleryItem $item): array => $item->toArray(), $this->items),
        ], static fn (mixed $value): bool => $value !== null);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
