<?php

namespace Kyzegs\Laracord\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Http\Client\Events\ResponseReceived;
use Kyzegs\Laracord\Listeners\SetRequestAllowance;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ResponseReceived::class => [
            SetRequestAllowance::class,
        ],
    ];
}
