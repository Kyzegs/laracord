<?php

declare(strict_types=1);

use Kyzegs\Laracord\Endpoints\EndpointCatalog;
use Kyzegs\Laracord\Enums\AuthenticationRequirement;

/**
 * A spec that matches the catalog exactly, for tests to break on purpose.
 *
 * @return array{paths: array<string, array<string, mixed>>}
 */
function specFromCatalog(): array
{
    $paths = [];
    foreach (EndpointCatalog::all() as $endpoints) {
        foreach ($endpoints as $endpoint) {
            $required = ($endpoint['auth'] ?? AuthenticationRequirement::REQUIRED) === AuthenticationRequirement::REQUIRED;
            $paths[$endpoint['path']][strtolower($endpoint['method']->value)] = [
                'security' => $required ? [['BotToken' => []]] : [(object) [], ['BotToken' => []]],
                'requestBody' => ['content' => [($endpoint['form'] ?? false) ? 'application/x-www-form-urlencoded' : 'application/json' => (object) []]],
            ];
        }
    }

    return ['paths' => $paths];
}

/**
 * @param  array<string, mixed>  $spec
 * @return array{code: int, output: string}
 */
function runCatalogCheck(array $spec): array
{
    $file = (string) tempnam(sys_get_temp_dir(), 'spec');
    file_put_contents($file, (string) json_encode($spec));

    exec(escapeshellarg(PHP_BINARY).' '.escapeshellarg(dirname(__DIR__, 2).'/bin/catalog-check.php').' '.escapeshellarg($file).' 2>&1', $lines, $code);
    unlink($file);

    return ['code' => $code, 'output' => implode("\n", $lines)];
}

it('passes when the catalog matches the spec', function (): void {
    $result = runCatalogCheck(specFromCatalog());

    expect($result['code'])->toBe(0, $result['output'])
        ->and($result['output'])->toContain('Catalog matches');
});

it('ignores allow-listed routes', function (): void {
    $spec = specFromCatalog();
    unset($spec['paths']['/oauth2/token']);
    $spec['paths']['/channels/{channel_id}/pins']['get'] = ['security' => [['BotToken' => []]]];

    expect(runCatalogCheck($spec)['code'])->toBe(0);
});

it('reports a route Discord added', function (): void {
    $spec = specFromCatalog();
    $spec['paths']['/brand/{new_id}/route']['post'] = ['security' => [['BotToken' => []]]];

    $result = runCatalogCheck($spec);

    expect($result['code'])->toBe(1)
        ->and($result['output'])->toContain('Missing from catalog: POST /brand/{}/route');
});

it('reports a catalogued route Discord does not have', function (): void {
    $spec = specFromCatalog();
    unset($spec['paths']['/users/@me']['get']);

    $result = runCatalogCheck($spec);

    expect($result['code'])->toBe(1)
        ->and($result['output'])->toContain("Not in Discord's spec: GET /users/@me (users.getCurrent)");
});

it('reports auth that disagrees with the spec', function (): void {
    $spec = specFromCatalog();
    $spec['paths']['/guilds/{guild_id}']['get']['security'] = [(object) []];

    $result = runCatalogCheck($spec);

    expect($result['code'])->toBe(1)
        ->and($result['output'])->toContain('Auth mismatch: GET /guilds/{} (guilds.get)');
});

it('reports a form route Discord takes as JSON', function (): void {
    $spec = specFromCatalog();
    $spec['paths']['/oauth2/token']['post']['requestBody']['content'] = ['application/json' => (object) []];

    $result = runCatalogCheck($spec);

    expect($result['code'])->toBe(1)
        ->and($result['output'])->toContain('Encoding mismatch: POST /oauth2/token (oauth2.token)');
});

it('honours a spec-wide security default', function (): void {
    $spec = specFromCatalog();
    $spec['security'] = [(object) []];
    unset($spec['paths']['/guilds/{guild_id}']['get']['security']);

    $result = runCatalogCheck($spec);

    expect($result['code'])->toBe(1)
        ->and($result['output'])->toContain('Auth mismatch: GET /guilds/{} (guilds.get)');
});

it('fails cleanly when the spec cannot be read', function (): void {
    exec(escapeshellarg(PHP_BINARY).' '.escapeshellarg(dirname(__DIR__, 2).'/bin/catalog-check.php').' /nonexistent/openapi.json 2>&1', $lines, $code);

    expect($code)->toBe(2)
        ->and(implode("\n", $lines))->toContain('Could not read');
});
