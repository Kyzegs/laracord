<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Tests;

use Kyzegs\Laracord\ServiceProvider as LaracordServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            LaracordServiceProvider::class,
        ];
    }
}
