<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * A Discord message or modal component that serializes to the Discord JSON shape.
 *
 * @extends Arrayable<string, mixed>
 */
interface Component extends Arrayable, JsonSerializable
{
    /** @return array<string, mixed> */
    public function toArray(): array;
}
