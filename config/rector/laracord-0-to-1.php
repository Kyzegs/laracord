<?php

declare(strict_types=1);

use Kyzegs\Laracord\Rector\HttpFacadeCallToLaracordRector;
use Kyzegs\Laracord\ServiceProvider;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

/**
 * Laracord 0.x -> 1.0 upgrade set.
 *
 * Import it from your own rector.php via LaracordSetList::UPGRADE_0_TO_1.
 */
return static function (RectorConfig $rectorConfig): void {
    // Rewrite application-command calls from the old Http facade + Routes constants
    // to the new resource-client API.
    $rectorConfig->rule(HttpFacadeCallToLaracordRector::class);

    // Straightforward class renames that are safe to apply blindly.
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        // The service provider was renamed and moved up a namespace level.
        'Kyzegs\\Laracord\\LaracordServiceProvider' => ServiceProvider::class,
    ]);
};
