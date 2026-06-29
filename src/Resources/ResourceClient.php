<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Resources;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Kyzegs\Laracord\Contracts\Client;
use Kyzegs\Laracord\Endpoints\EndpointCatalog;
use Kyzegs\Laracord\Enums\AuthenticationRequirement;
use Kyzegs\Laracord\Http\DiscordRequest;
use Kyzegs\Laracord\Http\DiscordResponse;
use Kyzegs\Laracord\ValueObjects\AuditLogReason;

readonly class ResourceClient
{
    public function __construct(private Client $discordClient, private string $resource) {}

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
            $definition['auth'] ?? AuthenticationRequirement::REQUIRED,
            $definition['form'] ?? false,
            $this->resource,
            $endpoint,
        ));
    }

    /** @param array<int|string, mixed> $arguments */
    public function __call(string $endpoint, array $arguments): DiscordResponse
    {
        /** @var array<string, string|int|\Stringable> $parameters */
        $parameters = $this->argument($arguments, 'parameters', 0, []);
        /** @var array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body */
        $body = $this->argument($arguments, 'body', 1, null);
        /** @var array<string, mixed> $query */
        $query = $this->argument($arguments, 'query', 2, []);
        /** @var list<array<string, mixed>> $files */
        $files = $this->argument($arguments, 'files', 3, []);
        /** @var AuditLogReason|null $auditLogReason */
        $auditLogReason = $this->argument($arguments, 'auditLogReason', 4, null);

        return $this->call(
            $endpoint,
            $parameters,
            $body,
            $query,
            $files,
            $auditLogReason,
        );
    }

    /** @param array<int|string, mixed> $arguments */
    private function argument(array $arguments, string $name, int $position, mixed $default): mixed
    {
        return array_key_exists($name, $arguments) ? $arguments[$name] : ($arguments[$position] ?? $default);
    }
}
