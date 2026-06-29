<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Resources\Generated;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Kyzegs\Laracord\Http\DiscordResponse;
use Kyzegs\Laracord\Resources\ResourceClient;
use Kyzegs\Laracord\ValueObjects\AuditLogReason;

/**
 * @method DiscordResponse get(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse edit(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse delete(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse editPermissions(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse deletePermission(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse followAnnouncement(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse triggerTyping(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse listPinnedMessages(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse pinMessage(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse unpinMessage(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse startThread(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse joinThread(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse addThreadMember(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse leaveThread(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse removeThreadMember(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse getThreadMember(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse listThreadMembers(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse listPublicArchivedThreads(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse listPrivateArchivedThreads(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 * @method DiscordResponse listJoinedPrivateArchivedThreads(array<string, string|int|\Stringable> $parameters = [], array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null $body = null, array<string, mixed> $query = [], list<array<string, mixed>> $files = [], ?AuditLogReason $auditLogReason = null)
 *
 * @internal Generated by bin/resources.php. Do not edit by hand.
 */
final readonly class ChannelsResource extends ResourceClient {}
