<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Testing;

use Closure;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Kyzegs\Laracord\Contracts\Client;
use Kyzegs\Laracord\Contracts\Factory;
use Kyzegs\Laracord\Http\DiscordRequest;
use Kyzegs\Laracord\Http\DiscordResponse;
use Kyzegs\Laracord\ValueObjects\OAuthAccessToken;
use PHPUnit\Framework\Assert as PHPUnit;
use Throwable;

/**
 * Test double swapped in by Laracord::fake(). Records every request the
 * application sends and answers with canned responses keyed by `resource.endpoint`.
 */
final class FakeLaracord implements Factory
{
    /** @var list<DiscordRequest> */
    private array $recorded = [];

    /** @var array<string, list<DiscordResponse|Throwable|Closure>> */
    private array $stubs = [];

    /** @param array<string, mixed> $stubs */
    public function __construct(array $stubs = [])
    {
        foreach ($stubs as $key => $value) {
            $this->stubs[$key] = is_array($value) ? array_values($value) : [$value];
        }
    }

    public function bot(): Client
    {
        return new FakeDiscordClient($this);
    }

    public function bearer(string|OAuthAccessToken $token): Client
    {
        return new FakeDiscordClient($this);
    }

    public function withoutAuthentication(): Client
    {
        return new FakeDiscordClient($this);
    }

    /**
     * Build a canned response for use as a stub value.
     *
     * @param  array<array-key, mixed>|string  $body
     * @param  array<string, string>  $headers
     */
    public static function response(array|string $body = '', int $status = 200, array $headers = []): DiscordResponse
    {
        $contents = is_array($body) ? json_encode($body, JSON_THROW_ON_ERROR) : $body;
        $headers = ['Content-Type' => 'application/json', ...$headers];

        return new DiscordResponse(new Psr7Response($status, $headers, $contents), $contents);
    }

    public function handle(DiscordRequest $request): DiscordResponse
    {
        $this->recorded[] = $request;

        $stub = $this->resolveStub($request);
        if ($stub instanceof Closure) {
            $stub = $stub($request);
        }

        if ($stub instanceof Throwable) {
            throw $stub;
        }

        return $stub ?? self::response(status: 200);
    }

    /** @return list<DiscordRequest> */
    public function recorded(?callable $callback = null): array
    {
        if ($callback === null) {
            return $this->recorded;
        }

        return array_values(array_filter($this->recorded, $callback));
    }

    public function assertSent(string $resource, string|callable|null $endpoint = null, ?callable $callback = null): void
    {
        if (is_callable($endpoint)) {
            $callback = $endpoint;
            $endpoint = null;
        }

        $label = $resource.($endpoint === null ? '' : '.'.$endpoint);
        PHPUnit::assertNotEmpty(
            $this->matching($resource, $endpoint, $callback),
            sprintf('Expected a Discord request matching [%s] but none was sent.', $label),
        );
    }

    public function assertNotSent(string $resource, string|callable|null $endpoint = null, ?callable $callback = null): void
    {
        if (is_callable($endpoint)) {
            $callback = $endpoint;
            $endpoint = null;
        }

        $label = $resource.($endpoint === null ? '' : '.'.$endpoint);
        PHPUnit::assertCount(
            0,
            $this->matching($resource, $endpoint, $callback),
            sprintf('Expected no Discord request matching [%s] but one was sent.', $label),
        );
    }

    public function assertNothingSent(): void
    {
        PHPUnit::assertCount(0, $this->recorded, 'Expected no Discord requests but some were sent.');
    }

    public function assertSentCount(int $count): void
    {
        PHPUnit::assertCount($count, $this->recorded);
    }

    /** @return list<DiscordRequest> */
    private function matching(string $resource, ?string $endpoint, ?callable $callback): array
    {
        return $this->recorded(static function (DiscordRequest $request) use ($resource, $endpoint, $callback): bool {
            if ($request->resource !== $resource) {
                return false;
            }

            if ($endpoint !== null && $request->endpoint !== $endpoint) {
                return false;
            }

            return $callback === null || $callback($request) === true;
        });
    }

    private function resolveStub(DiscordRequest $request): DiscordResponse|Throwable|Closure|null
    {
        foreach ($this->candidateKeys($request) as $key) {
            if (! isset($this->stubs[$key])) {
                continue;
            }

            if ($this->stubs[$key] === []) {
                continue;
            }

            return count($this->stubs[$key]) === 1
                ? $this->stubs[$key][0]
                : array_shift($this->stubs[$key]);
        }

        return null;
    }

    /** @return list<string> */
    private function candidateKeys(DiscordRequest $request): array
    {
        $resource = $request->resource;
        $endpoint = $request->endpoint;

        if ($resource === null) {
            return ['*'];
        }

        return $endpoint === null
            ? [$resource.'.*', $resource, '*']
            : [$resource.'.'.$endpoint, $resource.'.*', $resource, '*'];
    }
}
