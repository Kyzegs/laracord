<?php

declare(strict_types=1);

use Kyzegs\Laracord\LaracordManager;
use Kyzegs\Laracord\Tests\TestCase;

uses(TestCase::class);

it('registers manager and alias as singleton', function (): void {
    $laracordManager = resolve(LaracordManager::class);
    $alias = resolve('laracord');
    if (! $alias instanceof LaracordManager) {
        throw new RuntimeException('Laracord alias did not resolve the manager.');
    }

    expect($laracordManager)->toBe($alias);
    expect($alias)->toBe(resolve('laracord'));
});
