<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\RateLimit;

use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Cache\Repository;
use Kyzegs\GuzzleRateLimitMiddleware\Contracts\LockFactoryInterface;
use Kyzegs\GuzzleRateLimitMiddleware\Contracts\LockInterface;

final readonly class LaravelLockFactory implements LockFactoryInterface
{
    public function __construct(
        private Repository $repository,
        private string $prefix = 'laracord:lock:',
        private int $ttlSeconds = 60,
        private int $waitSeconds = 10,
    ) {}

    public function make(string $key): LockInterface
    {
        $store = $this->repository->getStore();
        if (! $store instanceof LockProvider) {
            throw new \RuntimeException('The configured Laracord cache store does not support atomic locks.');
        }

        return new LaravelLock($store->lock($this->prefix.hash('sha256', $key), $this->ttlSeconds), $this->waitSeconds);
    }
}
