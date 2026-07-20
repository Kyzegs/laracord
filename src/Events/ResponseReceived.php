<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Events;

use Kyzegs\Laracord\Http\DiscordRequest;
use Kyzegs\Laracord\Http\DiscordResponse;

/** Dispatched after a Discord request returns a successful (2xx) response. */
final readonly class ResponseReceived
{
    public function __construct(
        public DiscordRequest $request,
        public DiscordResponse $response,
        public string $requestId = '',
        public float $durationMs = 0.0,
        public int $attempts = 1,
    ) {}
}
