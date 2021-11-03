<?php

namespace Kyzegs\Laracord;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Kyzegs\Laracord\Socialite\DiscordProvider;
use Laravel\Socialite\Facades\Socialite;

class LaracordServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/laracord.php' => config_path('laracord.php'),
        ]);

        Socialite::extend('discord', function (Application $app) {
            return Socialite::buildProvider(DiscordProvider::class, $app['config']['laracord']);
        });
    }
}
