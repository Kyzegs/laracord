<?php

namespace Kyzegs\Laracord;

class BucketHash
{
    public function __construct(private readonly Route $route)
    {
    }

    private function key(): string
    {
        return sprintf('bucket_hashes:%s', $this->route->getKey());
    }

    public function get(): ?string
    {
        return cache()->get($this->key());
    }

    public function put(string $hash): bool
    {
        return cache()->put($this->key(), $hash);
    }

    public function missing(): bool
    {
        return cache()->missing($this->key());
    }
}
