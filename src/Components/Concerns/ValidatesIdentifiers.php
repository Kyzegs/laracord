<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components\Concerns;

trait ValidatesIdentifiers
{
    /**
     * @param  array<array-key, mixed>  $value
     * @param  array<int, true>  $ids
     * @param  array<string, true>  $customIds
     */
    private function validateUniqueIdentifiers(array $value, string $container, array &$ids = [], array &$customIds = []): void
    {
        if (isset($value['id']) && is_int($value['id']) && $value['id'] !== 0) {
            if (isset($ids[$value['id']])) {
                throw new \InvalidArgumentException('Component ids must be unique within a '.$container.'.');
            }

            $ids[$value['id']] = true;
        }

        if (isset($value['custom_id']) && is_string($value['custom_id'])) {
            if (isset($customIds[$value['custom_id']])) {
                throw new \InvalidArgumentException('Component custom_ids must be unique within a '.$container.'.');
            }

            $customIds[$value['custom_id']] = true;
        }

        foreach ($value as $child) {
            if (is_array($child)) {
                $this->validateUniqueIdentifiers($child, $container, $ids, $customIds);
            }
        }
    }
}
