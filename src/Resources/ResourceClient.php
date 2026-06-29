<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Resources;

use Generator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\LazyCollection;
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
        return $this->discordClient->send(self::buildRequest($this->resource, $endpoint, $parameters, $body, $query, $files, $auditLogReason));
    }

    /**
     * Build the DiscordRequest for an endpoint without sending it.
     *
     * @param  array<string, string|int|\Stringable>  $parameters
     * @param  array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null  $body
     * @param  array<string, mixed>  $query
     * @param  list<array<string, mixed>>  $files
     */
    public static function buildRequest(string $resource, string $endpoint, array $parameters = [], array|Arrayable|JsonSerializable|null $body = null, array $query = [], array $files = [], ?AuditLogReason $auditLogReason = null): DiscordRequest
    {
        $definition = EndpointCatalog::endpoint($resource, $endpoint);

        return new DiscordRequest(
            $definition['method'],
            $definition['path'],
            $parameters,
            $query,
            $body,
            $files,
            $auditLogReason,
            $definition['auth'] ?? AuthenticationRequirement::REQUIRED,
            $definition['form'] ?? false,
            $resource,
            $endpoint,
        );
    }

    /**
     * Lazily iterate a cursor-paginated endpoint, advancing by the id of the last
     * item on each page until a short page is returned.
     *
     * Defaults suit ascending-id collections (e.g. guild members, bans). For
     * reverse-chronological history pass the matching cursor, e.g. `before`.
     *
     * @param  array<string, string|int|\Stringable>  $parameters
     * @param  array<string, mixed>  $query
     * @return LazyCollection<int, mixed>
     */
    public function paginate(string $endpoint, array $parameters = [], array $query = [], string $cursor = 'after', int $perPage = 100, ?string $itemsKey = null): LazyCollection
    {
        return LazyCollection::make(function () use ($endpoint, $parameters, $query, $cursor, $perPage, $itemsKey): Generator {
            $cursorValue = $query[$cursor] ?? null;

            do {
                $pageQuery = [...$query, 'limit' => $perPage];
                if ($cursorValue !== null) {
                    $pageQuery[$cursor] = $cursorValue;
                }

                $items = $this->call($endpoint, $parameters, query: $pageQuery)->json($itemsKey);
                if (! is_array($items) || $items === []) {
                    break;
                }

                yield from $items;

                $last = end($items);
                $cursorValue = is_array($last) ? ($last['id'] ?? null) : null;
            } while ($cursorValue !== null && count($items) >= $perPage);
        });
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
