<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\RateLimit;

use Illuminate\Contracts\Cache\Lock;
use Kyzegs\GuzzleRateLimitMiddleware\Contracts\LockInterface;

final class LaravelLock implements LockInterface
{
    private bool $acquired = false;

    public function __construct(private readonly Lock $lock, private readonly int $waitSeconds) {}

    public function acquire(): void
    {
        $this->lock->block($this->waitSeconds);
        $this->acquired = true;
    }

    public function release(): void
    {
        if ($this->acquired) {
            $this->lock->release();
            $this->acquired = false;
        }
    }
}
