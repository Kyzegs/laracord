<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Events;

use Kyzegs\Laracord\Http\DiscordRequest;

/** Dispatched before a Discord request leaves the client. */
final readonly class RequestSending
{
    public function __construct(public DiscordRequest $request) {}
}
