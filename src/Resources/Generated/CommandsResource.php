<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Resources\Generated;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Kyzegs\Laracord\Http\DiscordResponse;
use Kyzegs\Laracord\Resources\ResourceClient;
use Kyzegs\Laracord\ValueObjects\AuditLogReason;

/**
 * @method DiscordResponse listGlobalCommands(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse createGlobalCommand(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse getGlobalCommand(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse editGlobalCommand(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse deleteGlobalCommand(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse bulkOverwriteGlobalCommands(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse listGuildCommands(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse createGuildCommand(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse getGuildCommand(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse editGuildCommand(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse deleteGuildCommand(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse bulkOverwriteGuildCommands(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse listGuildPermissions(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse getPermissions(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse editPermissions(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse batchEditPermissions(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 *
 * @internal Generated by bin/resources.php. Do not edit by hand.
 */
final readonly class CommandsResource extends ResourceClient {}
