<?php

namespace Kyzegs\Laracord\Models;

use Kyzegs\Laracord\Traits\HasAttributes;

class Model extends \Kyzegs\Model\Model
{
    use HasAttributes;

    /**
     * Create a new model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->syncOriginal();

        $this->fill($attributes);
    }
}
