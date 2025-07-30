<?php

namespace Spatie\LaravelRay;

class Ray
{
    public function __call($name, $arguments)
    {
        return $this;
    }
}
