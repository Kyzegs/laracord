<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Resources;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Kyzegs\Laracord\DiscordClient;
use Kyzegs\Laracord\Endpoints\EndpointCatalog;
use Kyzegs\Laracord\Enums\AuthenticationRequirement;
use Kyzegs\Laracord\Http\DiscordRequest;
use Kyzegs\Laracord\Http\DiscordResponse;
use Kyzegs\Laracord\ValueObjects\AuditLogReason;

final readonly class ResourceClient
{
    public function __construct(private DiscordClient $discordClient, private string $resource) {}

    /**
     * @param  array<string, string|int|\Stringable>  $parameters
     * @param  array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null  $body
     * @param  array<string, mixed>  $query
     * @param  list<array<string, mixed>>  $files
     */
    public function call(string $endpoint, array $parameters = [], array|Arrayable|JsonSerializable|null $body = null, array $query = [], array $files = [], ?AuditLogReason $auditLogReason = null): DiscordResponse
    {
        $definition = EndpointCatalog::endpoint($this->resource, $endpoint);

        return $this->discordClient->send(new DiscordRequest(
            $definition['method'],
            $definition['path'],
            $parameters,
            $query,
            $body,
            $files,
            $auditLogReason,
            $definition['auth'] ?? AuthenticationRequirement::Required,
            $definition['form'] ?? false,
        ));
    }

    /** @param array<int, mixed> $arguments */
    public function __call(string $endpoint, array $arguments): DiscordResponse
    {
        return $this->call(
            $endpoint,
            $arguments[0] ?? [],
            $arguments[1] ?? null,
            $arguments[2] ?? [],
            $arguments[3] ?? [],
            $arguments[4] ?? null,
        );
    }
}
