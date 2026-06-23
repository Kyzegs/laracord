<?php

declare(strict_types=1);

use Kyzegs\Laracord\Endpoints\EndpointCatalog;

it('defines unique endpoint names inside every resource', function (): void {
    foreach (EndpointCatalog::all() as $resource => $endpoints) {
        expect(array_keys($endpoints))->toHaveCount(count(array_unique(array_keys($endpoints))), $resource);
    }
});

it('contains modern discord resource families', function (): void {
    expect(array_keys(EndpointCatalog::all()))->toContain('entitlements', 'lobbies', 'polls', 'skus', 'soundboards', 'subscriptions');
});

it('retains every previously supported route while adding modern endpoints', function (): void {
    $count = array_sum(array_map(count(...), EndpointCatalog::all()));

    expect($count)->toBeGreaterThanOrEqual(185);
});
