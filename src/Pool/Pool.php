<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Pool;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Kyzegs\Laracord\Http\DiscordRequest;
use Kyzegs\Laracord\Resources\ResourceClient;
use Kyzegs\Laracord\ValueObjects\AuditLogReason;

/**
 * Collects unsent Discord requests for concurrent dispatch via DiscordClient::pool().
 */
final class Pool
{
    /**
     * Build an unsent request for a resource endpoint (e.g. `request('channels', 'get', ...)`).
     *
     * @param  array<string, string|int|\Stringable>  $parameters
     * @param  array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null  $body
     * @param  array<string, mixed>  $query
     * @param  list<array<string, mixed>>  $files
     */
    public function request(string $resource, string $endpoint, array $parameters = [], array|Arrayable|JsonSerializable|null $body = null, array $query = [], array $files = [], ?AuditLogReason $auditLogReason = null): DiscordRequest
    {
        return ResourceClient::buildRequest($resource, $endpoint, $parameters, $body, $query, $files, $auditLogReason);
    }
}
