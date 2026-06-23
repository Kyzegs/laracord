<?php

declare(strict_types=1);

namespace Kyzegs\Laracord;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Kyzegs\Laracord\Notifications\DiscordChannel;
use Kyzegs\Laracord\Socialite\DiscordProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\SocialiteManager;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the service provider.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config/laracord.php' => config_path('laracord.php')], 'laracord-config');
        }

        $this->app->afterResolving(SocialiteFactory::class, function (SocialiteManager $socialiteManager): void {
            $socialiteManager->extend('discord', fn (Application $application) => $socialiteManager->buildProvider(
                DiscordProvider::class,
                (array) $application->make(Repository::class)->get('laracord.oauth', []),
            ));
        });
        $this->app->afterResolving(ChannelManager::class, function (ChannelManager $channelManager): void {
            $channelManager->extend('discord', fn () => $this->app->make(DiscordChannel::class));
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laracord.php', 'laracord');

        $this->app->singleton(ClientFactory::class);
        $this->app->singleton(LaracordManager::class);
        $this->app->alias(LaracordManager::class, 'laracord');
    }
}
