<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Interactions;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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

    /** Read a command option value by name. */
    public function option(string $name, mixed $default = null): mixed
    {
        $options = $this->data('options', []);
        if (! is_array($options)) {
            return $default;
        }

        foreach ($options as $option) {
            if (is_array($option) && ($option['name'] ?? null) === $name) {
                return $option['value'] ?? $default;
            }
        }

        return $default;
    }
}
