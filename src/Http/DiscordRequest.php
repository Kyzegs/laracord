<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Http;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Kyzegs\Laracord\Enums\AuthenticationRequirement;
use Kyzegs\Laracord\Enums\HttpMethod;
use Kyzegs\Laracord\ValueObjects\AuditLogReason;
use Stringable;

final readonly class DiscordRequest
{
    /**
     * @param  array<string, Stringable|string|int>  $parameters
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>|Arrayable<string, mixed>|JsonSerializable|null  $body
     * @param  list<array<string, mixed>>  $files
     */
    public function __construct(
        public HttpMethod $method,
        public string $path,
        public array $parameters = [],
        public array $query = [],
        public array|Arrayable|JsonSerializable|null $body = null,
        public array $files = [],
        public ?AuditLogReason $reason = null,
        public AuthenticationRequirement $authentication = AuthenticationRequirement::Required,
        public bool $form = false,
    ) {
        if (! str_starts_with($path, '/')) {
            throw new \InvalidArgumentException('Discord request path must start with /.');
        }
    }

    public function resolvedPath(): string
    {
        $path = $this->path;
        foreach ($this->parameters as $name => $value) {
            $path = str_replace('{'.$name.'}', rawurlencode((string) $value), $path);
        }

        if (preg_match('/\{[^}]+}/', $path) === 1) {
            throw new \InvalidArgumentException('Missing Discord route parameter for '.$path.'.');
        }

        return $path;
    }

    /** @return array<string, mixed>|null */
    public function bodyArray(): ?array
    {
        return match (true) {
            $this->body === null => null,
            is_array($this->body) => $this->body,
            $this->body instanceof Arrayable => $this->body->toArray(),
            default => (array) $this->body->jsonSerialize(),
        };
    }
}
