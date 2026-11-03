<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Concerns\BuildsPayload;
use Kyzegs\Laracord\Components\Concerns\HasId;
use Kyzegs\Laracord\Components\Enums\ComponentType;

final class Checkbox implements Component
{
    use BuildsPayload;
    use HasId;

    private ?bool $default = null;

    private function __construct(private readonly string $customId)
    {
        self::assertCustomId($customId, 'Checkbox');
    }

    public static function make(string $customId): self
    {
        return new self($customId);
    }

    public function default(bool $default = true): self
    {
        $this->default = $default;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->withoutNulls([
            'type' => ComponentType::CHECKBOX->value,
            'id' => $this->id,
            'custom_id' => $this->customId,
            'default' => $this->default,
        ]);
    }
}
