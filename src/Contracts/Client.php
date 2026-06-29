<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Contracts;

use Kyzegs\Laracord\Http\DiscordRequest;
use Kyzegs\Laracord\Http\DiscordResponse;
use Kyzegs\Laracord\Pool\Pool;
use Kyzegs\Laracord\Resources\ResourceClient;
use Kyzegs\Laracord\ValueObjects\OAuthAccessToken;
use Throwable;

/**
 * A Discord HTTP client bound to a single authentication context.
 *
 * Implementations expose every resource as a magic accessor (e.g. `messages()`,
 * `guilds()`); see the generated `@method` annotations on DiscordClient.
 */
interface Client
{
    public function asBot(?string $token = null): self;

    public function asUser(string|OAuthAccessToken $token): self;

    public function withoutAuthentication(): self;

    public function resource(string $name): ResourceClient;

    public function send(DiscordRequest $discordRequest): DiscordResponse;

    /**
     * @param  callable(Pool): array<array-key, DiscordRequest>  $callback
     * @return array<array-key, DiscordResponse|Throwable>
     */
    public function pool(callable $callback): array;
}
