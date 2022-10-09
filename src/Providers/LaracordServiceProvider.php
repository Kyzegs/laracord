<?php

namespace Kyzegs\Laracord\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Kyzegs\Laracord\Providers\EventServiceProvider;
use Kyzegs\Laracord\Socialite\DiscordProvider;
use Laravel\Socialite\Facades\Socialite;

class LaracordServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the service provider.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
            $this->publishes([__DIR__.'/../../database/migrations' => database_path('migrations')], 'laracord-migrations');
            $this->publishes([__DIR__.'/../../laracord.php' => config_path('laracord.php')], 'laracord-config');
        }

        if (! $this->app->environment('testing')) {
            Socialite::extend('discord', function (Application $app) {
                return Socialite::buildProvider(DiscordProvider::class, $app['config']['laracord']);
            });
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/laracord.php', 'laracord');

        $this->app->register(EventServiceProvider::class);
    }
}
