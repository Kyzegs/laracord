<?php
if (!function_exists('ray')) {
    function ray(...$args) {
        static $ray;
        if (!$ray) {
            $ray = new class {
                public function __call($name, $arguments) { return $this; }
            };
        }
        return $ray;
    }
}

