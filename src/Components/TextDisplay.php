<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Concerns\BuildsPayload;
use Kyzegs\Laracord\Components\Concerns\HasId;
use Kyzegs\Laracord\Components\Enums\ComponentType;

final class TextDisplay implements Component
{
    use BuildsPayload;
    use HasId;

    private function __construct(private readonly string $content)
    {
        if ($content === '' || mb_strlen($content) > 4000) {
            throw new \InvalidArgumentException('Text display content must contain between 1 and 4000 characters.');
        }
    }

    public static function make(string $content): self
    {
        return new self($content);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->withoutNulls([
            'type' => ComponentType::TEXT_DISPLAY->value,
            'id' => $this->id,
            'content' => $this->content,
        ]);
    }
}
