<?php

namespace Kyzegs\Laracord\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use LogicException;

trait Cacheable
{
    /**
     * @return string
     */
    public function getCacheKeyAttribute(): string
    {
        throw new LogicException('Please implement the getCacheKeyAttribute method on your model.');
    }

    /**
     * @return string
     */
    private function getCacheKeyFromCollection(Collection $collection): string
    {
        return $collection->first()->cacheKey;
    }

    /**
     * @param Collection $items
     * @return Collection
     */
    protected function put(Collection $items): Collection
    {
        return $items->tap(fn () => Cache::put($this->getCacheKeyFromCollection($items), $items, 3600));
    }

    /**
     * @param Collection $items
     * @return Collection
     */
    protected function remember(Collection $items): Collection
    {
        return Cache::remember($this->getCacheKeyFromCollection($items), 3600, fn () => $items);
    }

    /**
     * @return mixed
     */
    protected function push(): static
    {
        if (Cache::has($this->cacheKey)) {
            Cache::pull($this->cacheKey)->push($this)->tap(fn (Collection $items) => $this->put($items));
        }

        return $this;
    }

    /**
     * @param  int  $guildId
     * @return mixed
     */
    protected function update(): static
    {
        if (Cache::has($this->cacheKey)) {
            $this->forget($this->id)->push($this)->tap(fn (Collection $items) => $this->put($items));
        }

        return $this;
    }

    /**
     * @param  int  $id
     * @return Collection
     */
    protected function forget(int $id): Collection
    {
        return Cache::pull($this->cacheKey)->reject(fn ($item) => $item->id === $id)->tap(fn (Collection $items) => $this->put($items));
    }
}
