<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Exceptions;

use Kyzegs\Laracord\Http\DiscordRequest;
use Kyzegs\Laracord\Http\DiscordResponse;

class DiscordHttpException extends DiscordException
{
    public function __construct(public readonly DiscordResponse $response, public readonly ?DiscordRequest $request = null)
    {
        $message = 'Discord request failed with HTTP '.$response->status().'.';
        try {
            $message = (string) ($response->json('message') ?? $message);
        } catch (\JsonException) {
        }

        parent::__construct($message, $response->status());
    }
}
