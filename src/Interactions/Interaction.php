<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Interactions;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Kyzegs\Laracord\Contracts\Client;
use Kyzegs\Laracord\Contracts\Factory;
use Kyzegs\Laracord\Interactions\Enums\InteractionType;

final readonly class Interaction
{
    /** @param array<string, mixed> $payload */
    public function __construct(public array $payload) {}

    /** Build an interaction from a verified Discord webhook request. */
    public static function from(Request $request): self
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->json()->all();

        return new self($payload);
    }

    public function id(): string
    {
        return (string) Arr::get($this->payload, 'id', '');
    }

    public function applicationId(): string
    {
        return (string) Arr::get($this->payload, 'application_id', '');
    }

    public function type(): int
    {
        return (int) Arr::get($this->payload, 'type', 0);
    }

    public function isPing(): bool
    {
        return $this->type() === InteractionType::PING->value;
    }

    public function token(): string
    {
        return (string) Arr::get($this->payload, 'token', '');
    }

    public function createdAt(): DateTimeImmutable
    {
        if (preg_match('/^\d+$/', $this->id()) !== 1) {
            throw new \LogicException('Interaction id must be a Discord snowflake.');
        }

        $milliseconds = ((int) $this->id() >> 22) + 1_420_070_400_000;

        return (new DateTimeImmutable('@'.intdiv($milliseconds, 1000)))
            ->setTimezone(new DateTimeZone('UTC'));
    }

    public function expiresAt(): DateTimeImmutable
    {
        return $this->createdAt()->modify('+15 minutes');
    }

    public function isExpired(?DateTimeInterface $at = null): bool
    {
        return ($at ?? new DateTimeImmutable('now', new DateTimeZone('UTC'))) >= $this->expiresAt();
    }

    public function context(Client|Factory $factory): InteractionContext
    {
        return new InteractionContext($this, $factory->withoutAuthentication());
    }

    public function data(?string $key = null, mixed $default = null): mixed
    {
        return $key === null ? Arr::get($this->payload, 'data', []) : Arr::get($this->payload, 'data.'.$key, $default);
    }

    /** The invoked command name (application command and autocomplete interactions). */
    public function commandName(): ?string
    {
        $name = $this->data('name');

        return $name === null ? null : (string) $name;
    }

    /** The component or modal custom_id (message component and modal submit interactions). */
    public function customId(): ?string
    {
        $customId = $this->data('custom_id');

        return $customId === null ? null : (string) $customId;
    }

    /**
     * Selected values of a message component (e.g. a select menu).
     *
     * @return list<mixed>
     */
    public function values(): array
    {
        $values = $this->data('values', []);

        return is_array($values) ? array_values($values) : [];
    }

    /** @return list<array<string, mixed>> */
    public function options(): array
    {
        $options = $this->data('options', []);

        return is_array($options) ? array_values(array_filter($options, is_array(...))) : [];
    }

    /** Read a command option value by dot-delimited name, including nested subcommands. */
    public function option(string $path, mixed $default = null): mixed
    {
        $option = $this->optionData($path);

        return $option !== null && array_key_exists('value', $option) ? $option['value'] : $default;
    }

    /** @return array<string, mixed>|null */
    public function optionData(string $path): ?array
    {
        $options = $this->options();
        foreach (explode('.', $path) as $name) {
            $match = null;
            foreach ($options as $option) {
                if (($option['name'] ?? null) === $name) {
                    $match = $option;

                    break;
                }
            }

            if ($match === null) {
                return null;
            }

            $options = isset($match['options']) && is_array($match['options'])
                ? array_values(array_filter($match['options'], is_array(...)))
                : [];
        }

        return $match;
    }

    public function resolved(string $type, ?string $id = null, mixed $default = null): mixed
    {
        $resolved = $this->data('resolved.'.$type, []);
        if (! is_array($resolved)) {
            return $id === null ? [] : $default;
        }

        return $id === null ? $resolved : ($resolved[$id] ?? $default);
    }

    /** @return array<array-key, array<string, mixed>> */
    public function resolvedUsers(): array
    {
        return $this->resolvedMap('users');
    }

    /** @return array<array-key, array<string, mixed>> */
    public function resolvedMembers(): array
    {
        return $this->resolvedMap('members');
    }

    /** @return array<array-key, array<string, mixed>> */
    public function resolvedRoles(): array
    {
        return $this->resolvedMap('roles');
    }

    /** @return array<array-key, array<string, mixed>> */
    public function resolvedChannels(): array
    {
        return $this->resolvedMap('channels');
    }

    /** @return array<array-key, array<string, mixed>> */
    public function resolvedMessages(): array
    {
        return $this->resolvedMap('messages');
    }

    /** @return array<array-key, array<string, mixed>> */
    public function resolvedAttachments(): array
    {
        return $this->resolvedMap('attachments');
    }

    /** @return array<array-key, array<string, mixed>> */
    private function resolvedMap(string $type): array
    {
        $resolved = $this->resolved($type, default: []);

        return is_array($resolved) ? array_filter($resolved, is_array(...)) : [];
    }
}
