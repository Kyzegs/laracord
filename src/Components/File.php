<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Concerns\HasId;
use Kyzegs\Laracord\Components\Enums\ComponentType;

final class File implements Component
{
    use HasId;

    private ?bool $spoiler = null;

    private function __construct(private readonly string $url) {}

    public static function make(string $filename): self
    {
        $url = str_starts_with($filename, 'attachment://') ? $filename : 'attachment://'.$filename;

        if ($url === 'attachment://' || mb_strlen($url) > 2048) {
            throw new \InvalidArgumentException('File component requires a valid attachment filename.');
        }

        return new self($url);
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
            'type' => ComponentType::FILE->value,
            'id' => $this->id,
            'file' => ['url' => $this->url],
            'spoiler' => $this->spoiler,
        ], static fn (mixed $value): bool => $value !== null);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
