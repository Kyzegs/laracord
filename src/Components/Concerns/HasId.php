<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components\Concerns;

trait HasId
{
    private ?int $id = null;

    public function id(int $id): static
    {
        if ($id < 0 || $id > 2_147_483_647) {
            throw new \InvalidArgumentException('Component id must be between 0 and 2147483647.');
        }

        $this->id = $id;

        return $this;
    }
}
