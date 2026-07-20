<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Http;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use JsonException;
use Psr\Http\Message\ResponseInterface;

final readonly class DiscordResponse
{
    public function __construct(private ResponseInterface $response, private string $body) {}

    public function status(): int
    {
        return $this->response->getStatusCode();
    }

    /** @return array<string, list<string>> */
    public function headers(): array
    {
        return array_map(array_values(...), $this->response->getHeaders());
    }

    public function body(): string
    {
        return $this->body;
    }

    public function isNoContent(): bool
    {
        if ($this->status() === 204) {
            return true;
        }

        return $this->body === '';
    }

    /** @return array{status:int,rate_limit:array{limit:?int,remaining:?int,reset_after:?float,bucket:?string,scope:?string}} */
    public function formatForTelescope(): array
    {
        return ['status' => $this->status(), 'rate_limit' => $this->rateLimit()];
    }

    /** @throws JsonException */
    public function json(?string $key = null, mixed $default = null): mixed
    {
        if ($this->body === '') {
            return $key === null ? null : $default;
        }

        $decoded = json_decode($this->body, true, 512, JSON_THROW_ON_ERROR);

        return $key === null ? $decoded : Arr::get($decoded, $key, $default);
    }

    /**
     * Wrap the decoded JSON (or a sub-key) in a Collection.
     *
     * @return Collection<array-key, mixed>
     *
     * @throws JsonException
     */
    public function collect(?string $key = null): Collection
    {
        $value = $this->json($key);

        return new Collection(is_array($value) ? $value : []);
    }

    /**
     * Wrap the decoded JSON body in a Fluent for ergonomic attribute access.
     *
     * @return Fluent<string, mixed>
     *
     * @throws JsonException
     */
    public function fluent(): Fluent
    {
        $value = $this->json();

        return new Fluent(is_array($value) ? $value : []);
    }

    /** @return array{limit:?int,remaining:?int,reset_after:?float,bucket:?string,scope:?string} */
    public function rateLimit(): array
    {
        $header = fn (string $name): ?string => $this->response->hasHeader($name) ? $this->response->getHeaderLine($name) : null;

        return [
            'limit' => ($value = $header('X-RateLimit-Limit')) === null ? null : (int) $value,
            'remaining' => ($value = $header('X-RateLimit-Remaining')) === null ? null : (int) $value,
            'reset_after' => ($value = $header('X-RateLimit-Reset-After')) === null ? null : (float) $value,
            'bucket' => $header('X-RateLimit-Bucket'),
            'scope' => $header('X-RateLimit-Scope'),
        ];
    }
}
