<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Resources\Concerns;

use Illuminate\Support\Str;
use Kyzegs\Laracord\Endpoints\EndpointCatalog;
use Kyzegs\Laracord\Resources\ResourceClient;

trait ResolvesResources
{
    public function resource(string $name): ResourceClient
    {
        $class = 'Kyzegs\\Laracord\\Resources\\Generated\\'.Str::studly($name).'Resource';

        if (class_exists($class) && is_subclass_of($class, ResourceClient::class)) {
            return new $class($this, $name);
        }

        return new ResourceClient($this, $name);
    }

    /** @param array<int, mixed> $arguments */
    public function __call(string $name, array $arguments): ResourceClient
    {
        if ($arguments === [] && EndpointCatalog::hasResource($name)) {
            return $this->resource($name);
        }

        throw new \BadMethodCallException('Unknown Discord resource '.$name.'.');
    }
}
