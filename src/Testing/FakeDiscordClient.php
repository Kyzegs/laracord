<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Testing;

use Illuminate\Support\Str;
use Kyzegs\Laracord\Contracts\Client;
use Kyzegs\Laracord\Endpoints\EndpointCatalog;
use Kyzegs\Laracord\Http\DiscordRequest;
use Kyzegs\Laracord\Http\DiscordResponse;
use Kyzegs\Laracord\Pool\Pool;
use Kyzegs\Laracord\Resources\ResourceClient;
use Kyzegs\Laracord\ValueObjects\OAuthAccessToken;
use Throwable;

/**
 * Stand-in for DiscordClient used by FakeLaracord. Records every request and
 * returns canned responses instead of hitting Discord.
 */
final readonly class FakeDiscordClient implements Client
{
    public function __construct(private FakeLaracord $fake) {}

    public function asBot(?string $token = null): self
    {
        return $this;
    }

    public function asUser(string|OAuthAccessToken $token): self
    {
        return $this;
    }

    public function withoutAuthentication(): self
    {
        return $this;
    }

    public function resource(string $name): ResourceClient
    {
        $class = 'Kyzegs\\Laracord\\Resources\\Generated\\'.Str::studly($name).'Resource';

        if (class_exists($class) && is_subclass_of($class, ResourceClient::class)) {
            return new $class($this, $name);
        }

        return new ResourceClient($this, $name);
    }

    /** @param array<int, mixed> $arguments */
    public function __call(string $name, array $arguments): ResourceClient
    {
        if ($arguments === [] && EndpointCatalog::hasResource($name)) {
            return $this->resource($name);
        }

        throw new \BadMethodCallException('Unknown Discord resource '.$name.'.');
    }

    public function send(DiscordRequest $discordRequest): DiscordResponse
    {
        return $this->fake->handle($discordRequest);
    }

    /**
     * @param  callable(Pool): array<array-key, DiscordRequest>  $callback
     * @return array<array-key, DiscordResponse|Throwable>
     */
    public function pool(callable $callback): array
    {
        $results = [];
        foreach ($callback(new Pool) as $key => $discordRequest) {
            try {
                $results[$key] = $this->fake->handle($discordRequest);
            } catch (Throwable $exception) {
                $results[$key] = $exception;
            }
        }

        return $results;
    }
}
