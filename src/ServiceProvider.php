<?php

declare(strict_types=1);

namespace Kyzegs\Laracord;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Kyzegs\Laracord\Socialite\DiscordProvider;
use Laravel\Socialite\Facades\Socialite;

class ServiceProvider extends BaseServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap the service provider.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config/laracord.php' => config_path('laracord.php')], 'laracord-config');
        }

        if (! $this->app->environment('testing')) {
            Socialite::extend('discord', static fn (Application $application) => Socialite::buildProvider(DiscordProvider::class, $application['config']['laracord']));
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laracord.php', 'laracord');

        $this->app->singleton(Client::class, static fn (): Client => Laracord::factory()->make());
        $this->app->alias(Client::class, 'laracord');
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [Client::class, 'laracord'];
    }
}
