<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\RateLimit;

use Illuminate\Contracts\Cache\Repository;
use Kyzegs\GuzzleRateLimitMiddleware\Contracts\StoreInterface;

final readonly class LaravelStore implements StoreInterface
{
    public function __construct(private Repository $repository, private string $prefix = 'laracord:rate-limit:') {}

    public function get(string $key): ?array
    {
        $value = $this->repository->get($this->key($key));

        return is_array($value) ? $value : null;
    }

    public function put(string $key, array $data, int $ttl): bool
    {
        return $this->repository->put($this->key($key), $data, $ttl);
    }

    public function forget(string $key): bool
    {
        return $this->repository->forget($this->key($key));
    }

    private function key(string $key): string
    {
        return $this->prefix.hash('sha256', $key);
    }
}
