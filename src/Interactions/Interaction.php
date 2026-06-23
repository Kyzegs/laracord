<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Interactions;

use Illuminate\Support\Arr;

final readonly class Interaction
{
    /** @param array<string, mixed> $payload */
    public function __construct(public array $payload) {}

    public function id(): string
    {
        return (string) Arr::get($this->payload, 'id', '');
    }

    public function type(): int
    {
        return (int) Arr::get($this->payload, 'type', 0);
    }

    public function token(): string
    {
        return (string) Arr::get($this->payload, 'token', '');
    }

    public function data(?string $key = null, mixed $default = null): mixed
    {
        return $key === null ? Arr::get($this->payload, 'data', []) : Arr::get($this->payload, 'data.'.$key, $default);
    }
}
