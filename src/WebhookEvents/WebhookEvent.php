<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\WebhookEvents;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use JsonException;

final readonly class WebhookEvent
{
    /** @param array<string, mixed> $payload */
    public function __construct(public array $payload)
    {
        if (! $this->isValidPayload($payload)) {
            throw new InvalidArgumentException('Invalid Discord webhook event payload.');
        }
    }

    public static function from(Request $request): self
    {
        try {
            $payload = json_decode($request->getContent(), true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $jsonException) {
            throw new InvalidArgumentException('Invalid Discord webhook event payload.', $jsonException->getCode(), previous: $jsonException);
        }

        if (! is_array($payload)) {
            throw new InvalidArgumentException('Invalid Discord webhook event payload.');
        }

        /** @var array<string, mixed> $payload */
        return new self($payload);
    }

    public function version(): int
    {
        return $this->payload['version'];
    }

    public function applicationId(): string
    {
        return $this->payload['application_id'];
    }

    public function type(): int
    {
        return $this->payload['type'];
    }

    public function isPing(): bool
    {
        return $this->type() === 0;
    }

    public function eventType(): ?string
    {
        return $this->isPing() ? null : $this->payload['event']['type'];
    }

    public function timestamp(): ?string
    {
        return $this->isPing() ? null : $this->payload['event']['timestamp'];
    }

    public function data(?string $key = null, mixed $default = null): mixed
    {
        $data = $this->isPing() ? [] : ($this->payload['event']['data'] ?? []);

        return $key === null ? $data : Arr::get($data, $key, $default);
    }

    /** @param array<string, mixed> $payload */
    private function isValidPayload(array $payload): bool
    {
        $type = $payload['type'] ?? null;

        if (($payload['version'] ?? null) !== 1
            || ! $this->hasNonEmptyString($payload, 'application_id')
            || ! is_int($type)
            || ! in_array($type, [0, 1], true)) {
            return false;
        }

        return $type === 0 || $this->isValidEvent($payload['event'] ?? null);
    }

    private function isValidEvent(mixed $event): bool
    {
        return is_array($event)
            && $this->hasNonEmptyString($event, 'type')
            && $this->hasNonEmptyString($event, 'timestamp')
            && (! array_key_exists('data', $event) || is_array($event['data']));
    }

    /** @param array<array-key, mixed> $payload */
    private function hasNonEmptyString(array $payload, string $key): bool
    {
        return isset($payload[$key]) && is_string($payload[$key]) && $payload[$key] !== '';
    }
}
