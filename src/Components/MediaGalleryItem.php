<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/** @implements Arrayable<string, mixed> */
final class MediaGalleryItem implements Arrayable, JsonSerializable
{
    private ?string $description = null;

    private ?bool $spoiler = null;

    private function __construct(private readonly string $url)
    {
        if ($url === '' || mb_strlen($url) > 2048) {
            throw new \InvalidArgumentException('Media URL must contain between 1 and 2048 characters.');
        }
    }

    public static function make(string $url): self
    {
        return new self($url);
    }

    public function description(string $description): self
    {
        if ($description === '' || mb_strlen($description) > 1024) {
            throw new \InvalidArgumentException('Media description must contain between 1 and 1024 characters.');
        }

        $this->description = $description;

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
        return array_filter([
            'media' => ['url' => $this->url],
            'description' => $this->description,
            'spoiler' => $this->spoiler,
        ], static fn (mixed $value): bool => $value !== null);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
