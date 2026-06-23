<?php

declare(strict_types=1);

use Kyzegs\Laracord\Rector\HttpFacadeCallToLaracordRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(HttpFacadeCallToLaracordRector::class);
};
