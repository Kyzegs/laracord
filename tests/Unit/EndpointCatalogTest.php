<?php

declare(strict_types=1);

use Kyzegs\Laracord\Endpoints\EndpointCatalog;
use Kyzegs\Laracord\Enums\AuthenticationRequirement;

it('uses rooted paths with snake case placeholders', function (): void {
    foreach (EndpointCatalog::all() as $resource => $endpoints) {
        foreach ($endpoints as $name => $endpoint) {
            expect($endpoint['path'])->toMatch('#^(/[\w.@-]+|/\{[a-z0-9_]+})+$#', $resource.'.'.$name);
        }
    }
});

it('keeps routes that used to be wrong on the paths Discord documents', function (string $resource, string $endpoint, string $method, string $path): void {
    $definition = EndpointCatalog::endpoint($resource, $endpoint);

    expect($definition['method']->value)->toBe($method)
        ->and($definition['path'])->toBe($path);
})->with([
    ['scheduledEvents', 'list', 'GET', '/guilds/{guild_id}/scheduled-events'],
    ['scheduledEvents', 'create', 'POST', '/guilds/{guild_id}/scheduled-events'],
    ['scheduledEvents', 'get', 'GET', '/guilds/{guild_id}/scheduled-events/{guild_scheduled_event_id}'],
    ['scheduledEvents', 'edit', 'PATCH', '/guilds/{guild_id}/scheduled-events/{guild_scheduled_event_id}'],
    ['scheduledEvents', 'delete', 'DELETE', '/guilds/{guild_id}/scheduled-events/{guild_scheduled_event_id}'],
    ['scheduledEvents', 'listUsers', 'GET', '/guilds/{guild_id}/scheduled-events/{guild_scheduled_event_id}/users'],
    ['guilds', 'editOnboarding', 'PUT', '/guilds/{guild_id}/onboarding'],
    ['roleConnections', 'getUserConnection', 'GET', '/users/@me/applications/{application_id}/role-connection'],
    ['roleConnections', 'editUserConnection', 'PUT', '/users/@me/applications/{application_id}/role-connection'],
    ['lobbies', 'moderateMessage', 'PUT', '/lobbies/{lobby_id}/messages/{message_id}/moderation-metadata'],
    ['channels', 'listPinnedMessages', 'GET', '/channels/{channel_id}/messages/pins'],
    ['channels', 'pinMessage', 'PUT', '/channels/{channel_id}/messages/pins/{message_id}'],
    ['channels', 'unpinMessage', 'DELETE', '/channels/{channel_id}/messages/pins/{message_id}'],
]);

it('lets public routes be called without a token', function (string $resource, string $endpoint): void {
    expect(EndpointCatalog::endpoint($resource, $endpoint)['auth'] ?? null)->toBe(AuthenticationRequirement::OPTIONAL);
})->with([
    ['guilds', 'getWidget'],
    ['guilds', 'getWidgetImage'],
    ['invites', 'get'],
    ['stickers', 'listPacks'],
    ['templates', 'getGuildTemplate'],
    ['gateway', 'get'],
    ['oauth2', 'getPublicKeys'],
]);

it('no longer lists routes Discord removed', function (string $resource, string $endpoint): void {
    expect(array_key_exists($endpoint, EndpointCatalog::all()[$resource]))->toBeFalse();
})->with([
    ['guilds', 'create'],
    ['guilds', 'delete'],
    ['guilds', 'modifyMfaLevel'],
    ['templates', 'createGuildFromTemplate'],
    ['commands', 'batchEditPermissions'],
]);
