<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use Kyzegs\Laracord\Endpoints\EndpointCatalog;

$lines = [
    '# Endpoint Catalog',
    '',
    'Audited against Discord HTTP documentation on '.EndpointCatalog::AUDITED_AT.'.',
    '',
    'Call any entry with `$client->{resource}()->call($endpoint, $parameters, $body, $query, $files, $reason)`.',
    '',
];

foreach (EndpointCatalog::all() as $resource => $endpoints) {
    if ($endpoints === []) {
        continue;
    }
    $lines[] = '## '.ucwords(preg_replace('/(?<!^)[A-Z]/', ' $0', $resource));
    $lines[] = '';
    $lines[] = '| Endpoint | Method | Path |';
    $lines[] = '|---|---:|---|';
    foreach ($endpoints as $name => $endpoint) {
        $lines[] = sprintf('| `%s` | `%s` | `%s` |', $name, $endpoint['method']->value, $endpoint['path']);
    }
    $lines[] = '';
}

file_put_contents(__DIR__.'/docs/api/endpoints.md', implode(PHP_EOL, $lines).PHP_EOL);
