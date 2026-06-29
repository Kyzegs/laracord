<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Events;

use Kyzegs\Laracord\Http\DiscordRequest;
use Throwable;

/** Dispatched when a Discord request fails with a transport or HTTP error. */
final readonly class RequestFailed
{
    public function __construct(public DiscordRequest $request, public Throwable $exception) {}
}
