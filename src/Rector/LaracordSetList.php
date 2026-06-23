<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Rector;

/**
 * Rector set lists shipped with Laracord.
 *
 * Import a set in your own `rector.php`:
 *
 *     use Kyzegs\Laracord\Rector\LaracordSetList;
 *
 *     return RectorConfig::configure()
 *         ->withPaths([__DIR__.'/app'])
 *         ->withSets([LaracordSetList::UPGRADE_0_TO_1]);
 */
final class LaracordSetList
{
    /**
     * Upgrades a code base from Laracord 0.x to 1.0.
     */
    public const string UPGRADE_0_TO_1 = __DIR__.'/../../config/rector/laracord-0-to-1.php';
}
