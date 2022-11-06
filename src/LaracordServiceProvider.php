<?php

namespace Kyzegs\Laracord;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use Kyzegs\Laracord\Channels\DiscordChannel;
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
            $this->publishes([__DIR__.'/../config/laracord.php' => config_path('laracord.php')], 'laracord-config');
        }

        Socialite::extend('discord', function (Application $app) {
            return Socialite::buildProvider(DiscordProvider::class, $app['config']['laracord']);
        });

        Notification::extend('discord', function (Application $app) {
            return new DiscordChannel();
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laracord.php', 'laracord');
    }
}
