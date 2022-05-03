<?php

namespace Kyzegs\Laracord\Listeners;

use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Kyzegs\Laracord\Services\RateLimiter;

class SetRequestAllowance
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(ResponseReceived $event)
    {
        Log::debug(
            Str::of('? ?')
                ->replaceArray('?', [$event->request->method(), $event->request->url()])
                ->when($event->response->status() !== 204, fn ($string) => $string->append(' has received ?')->replace('?', $event->response->body()))
        );

        RateLimiter::setRequestAllowance($event->request, $event->response);
    }
}
