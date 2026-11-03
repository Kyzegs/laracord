<?php

declare(strict_types=1);

/*
 * Compare the EndpointCatalog with Discord's OpenAPI spec.
 *
 *   $ composer catalog:check
 *   $ php -f ./bin/catalog-check.php -- path/to/openapi.json
 */

use Kyzegs\Laracord\Endpoints\EndpointCatalog;
use Kyzegs\Laracord\Enums\AuthenticationRequirement;

require __DIR__.'/../vendor/autoload.php';

const SPEC_URL = 'https://raw.githubusercontent.com/discord/discord-api-spec/main/specs/openapi.json';

// Documented by Discord but absent from its OpenAPI spec.
const DOCS_ONLY = [
    'GET /applications/{}/skus',
    'POST /oauth2/token',
    'POST /oauth2/token/revoke',
    'GET /applications/{}/users/{}/identities/{}/profile',
    'PATCH /applications/{}/users/{}/identities/{}/profile',
    'GET /users/{}/application-identities/{}',
    'GET /applications/{}/application-identities/{}/{}',
    'POST /users/{}/application-identities/{}/{}/{}/delete',
];

// In the spec but deliberately not catalogued: Discord deprecated them in favour of /messages/pins.
const DEPRECATED = [
    'GET /channels/{}/pins',
    'PUT /channels/{}/pins/{}',
    'DELETE /channels/{}/pins/{}',
];

$source = $argv[1] ?? SPEC_URL;
$contents = @file_get_contents($source, context: stream_context_create(['http' => ['timeout' => 30]]));
if ($contents === false) {
    fwrite(STDERR, 'Could not read '.$source.PHP_EOL);
    exit(2);
}

/** @var array{paths: array<string, array<string, mixed>>, security?: list<array<string, mixed>>} $spec */
$spec = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);

$route = static fn (string $method, string $path): string => strtoupper($method).' '.preg_replace('/\{[^}]+}/', '{}', $path);

$operations = [];
foreach ($spec['paths'] as $path => $item) {
    foreach ($item as $method => $operation) {
        if (in_array($method, ['get', 'post', 'put', 'patch', 'delete'], true)) {
            $operations[$route($method, $path)] = $operation;
        }
    }
}

$catalog = [];
foreach (EndpointCatalog::all() as $resource => $endpoints) {
    foreach ($endpoints as $name => $endpoint) {
        $catalog[$route($endpoint['method']->value, $endpoint['path'])] = ['name' => $resource.'.'.$name, ...$endpoint];
    }
}

$problems = [];

foreach (array_diff_key($operations, $catalog, array_flip(DEPRECATED)) as $key => $operation) {
    $problems[] = 'Missing from catalog: '.$key;
}

foreach (array_diff_key($catalog, $operations, array_flip(DOCS_ONLY)) as $key => $endpoint) {
    $problems[] = 'Not in Discord\'s spec: '.$key.' ('.$endpoint['name'].')';
}

foreach (array_intersect_key($catalog, $operations) as $key => $endpoint) {
    $operation = $operations[$key];

    // An empty requirement object is how OpenAPI marks a route as callable anonymously.
    $anonymous = in_array([], $operation['security'] ?? $spec['security'] ?? [], true);
    $required = ($endpoint['auth'] ?? AuthenticationRequirement::REQUIRED) === AuthenticationRequirement::REQUIRED;
    if ($required === $anonymous) {
        $problems[] = 'Auth mismatch: '.$key.' ('.$endpoint['name'].') is '.($required ? 'required' : 'not required').' here, Discord '.($anonymous ? 'allows' : 'forbids').' anonymous calls';
    }

    $contentTypes = array_keys($operation['requestBody']['content'] ?? []);
    if (($endpoint['form'] ?? false) && ! in_array('application/x-www-form-urlencoded', $contentTypes, true)) {
        $problems[] = 'Encoding mismatch: '.$key.' ('.$endpoint['name'].') sends a form, Discord accepts '.implode(', ', $contentTypes);
    }

    if (! ($endpoint['form'] ?? false) && $contentTypes === ['application/x-www-form-urlencoded']) {
        $problems[] = 'Encoding mismatch: '.$key.' ('.$endpoint['name'].') sends JSON, Discord only accepts a form';
    }
}

if ($problems !== []) {
    echo implode(PHP_EOL, $problems).PHP_EOL.PHP_EOL.count($problems).' difference(s) from Discord\'s spec.'.PHP_EOL;
    exit(1);
}

echo 'Catalog matches Discord\'s spec ('.count($catalog).' routes).'.PHP_EOL;
