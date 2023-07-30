<?php

namespace Kyzegs\Laracord;

use Illuminate\Contracts\Cache\Lock;

class Bucket
{
    private BucketHash $bucketHash;

    public function __construct(private Route $route)
    {
        $this->bucketHash = new BucketHash($route);
    }

    private function key(): string
    {
        return sprintf('buckets:%s:%s', $this->bucketHash->get() ?? $this->route->getKey(), $this->route->getMajorParameters());
    }

    public function get(): Ratelimit
    {
        return cache()->get($this->key()) ?? new Ratelimit();
    }

    public function put(Ratelimit $ratelimit): bool
    {
        return cache()->put($this->key(), $ratelimit);
    }

    public function forget(): bool
    {
        return cache()->forget($this->key());
    }

    public function lock(): Lock
    {
        return cache()->lock($this->key());
    }
}
