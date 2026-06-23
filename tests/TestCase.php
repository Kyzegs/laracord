<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Tests;

use Illuminate\Foundation\Application;
use Kyzegs\Laracord\ServiceProvider as LaracordServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function defineEnvironment($app): void
    {
        $app['config']->set('cache.default', 'array');
        $app['config']->set('laracord.bot_token', 'test-token');
    }

    /**
     * Get package providers.
     *
     * @param  Application  $app
     * @return list<class-string>
     */
    protected function getPackageProviders($app)
    {
        return [
            LaracordServiceProvider::class,
        ];
    }
}
