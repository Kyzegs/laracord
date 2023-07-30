<?php

namespace Kyzegs\Laracord;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Psr\Http\Message\ResponseInterface;

class Ratelimit
{
    private int $limit = 1;

    private int $remaining = 1;

    private float $resetAfter = 0.0;

    private float $reset = 0.0;

    private bool $dirty = false;

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getRemaining(): int
    {
        return $this->remaining;
    }

    public function getResetAfter(): float
    {
        return $this->resetAfter;
    }

    public function getReset(): float
    {
        return $this->reset;
    }

    public function isDirty(): bool
    {
        return $this->dirty;
    }

    public function reset(): void
    {
        $this->remaining = $this->limit;
        $this->resetAfter = 0.0;
        $this->dirty = false;
    }

    public function update(ResponseInterface $response): static
    {
        $limit = (int) Arr::get($response->getHeader('X-Ratelimit-Limit'), 0, 1);
        $remaining = (int) Arr::get($response->getHeader('X-Ratelimit-Remaining'), 0, 0);
        $resetAfter = Arr::get($response->getHeader('X-Ratelimit-Reset-After'), 0);
        $reset = (float) Arr::get($response->getHeader('X-Ratelimit-Reset'), 0);

        $this->limit = $limit;
        $this->reset = $reset;

        if ($this->dirty) {
            $this->remaining = min($remaining, $limit);
        } else {
            $this->remaining = $remaining;
            $this->dirty = true;
        }

        if (! $resetAfter) {
            $this->resetAfter = Carbon::createFromTimestamp($reset)->subtract(Carbon::now('UTC'))->get('second');
        } else {
            $this->resetAfter = (float) $resetAfter;
        }

        return $this;
    }
}
