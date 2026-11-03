<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components\Concerns;

trait BuildsPayload
{
    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function withoutNulls(array $payload): array
    {
        return array_filter($payload, static fn (mixed $value): bool => $value !== null);
    }

    private static function assertCustomId(string $customId, string $component): void
    {
        if ($customId === '' || mb_strlen($customId) > 100) {
            throw new \InvalidArgumentException($component.' custom_id must contain between 1 and 100 characters.');
        }
    }
}
