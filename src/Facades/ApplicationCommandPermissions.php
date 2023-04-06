<?php

namespace Kyzegs\Laracord\Facades;

use Illuminate\Support\Facades\Facade;

class ApplicationCommandPermissions extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Kyzegs\Laracord\Models\ApplicationCommandPermissions::class;
    }
}
