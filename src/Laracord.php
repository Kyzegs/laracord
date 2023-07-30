<?php

namespace Kyzegs\Laracord;

class Laracord
{
    public static function client(): Client
    {
        return self::factory()->make();
    }

    public static function factory(): Factory
    {
        return new Factory();
    }
}
