<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Endpoints;

use Kyzegs\Laracord\Enums\AuthenticationRequirement;
use Kyzegs\Laracord\Enums\HttpMethod;

final class EndpointCatalog
{
    public const AUDITED_AT = '2026-06-23';

    /** @return array<string, array<string, array{method:HttpMethod,path:string,auth?:AuthenticationRequirement,form?:bool}>> */
    public static function all(): array
    {
        $modern = [
            'applications' => [
                'getCurrent' => ['method' => HttpMethod::Get, 'path' => '/applications/@me'],
                'editCurrent' => ['method' => HttpMethod::Patch, 'path' => '/applications/@me'],
                'getActivityInstance' => ['method' => HttpMethod::Get, 'path' => '/applications/{application_id}/activity-instances/{instance_id}'],
            ],
            'commands' => [
                'listGlobal' => ['method' => HttpMethod::Get, 'path' => '/applications/{application_id}/commands'],
                'createGlobal' => ['method' => HttpMethod::Post, 'path' => '/applications/{application_id}/commands'],
                'listGuild' => ['method' => HttpMethod::Get, 'path' => '/applications/{application_id}/guilds/{guild_id}/commands'],
                'createGuild' => ['method' => HttpMethod::Post, 'path' => '/applications/{application_id}/guilds/{guild_id}/commands'],
            ],
            'channels' => self::crud('/channels/{channel_id}'),
            'messages' => [
                'list' => ['method' => HttpMethod::Get, 'path' => '/channels/{channel_id}/messages'],
                'get' => ['method' => HttpMethod::Get, 'path' => '/channels/{channel_id}/messages/{message_id}'],
                'create' => ['method' => HttpMethod::Post, 'path' => '/channels/{channel_id}/messages'],
                'edit' => ['method' => HttpMethod::Patch, 'path' => '/channels/{channel_id}/messages/{message_id}'],
                'delete' => ['method' => HttpMethod::Delete, 'path' => '/channels/{channel_id}/messages/{message_id}'],
            ],
            'guilds' => self::crud('/guilds/{guild_id}') + [
                'listMembers' => ['method' => HttpMethod::Get, 'path' => '/guilds/{guild_id}/members'],
                'listChannels' => ['method' => HttpMethod::Get, 'path' => '/guilds/{guild_id}/channels'],
            ],
            'emojis' => self::crud('/guilds/{guild_id}/emojis/{emoji_id}') + [
                'listApplication' => ['method' => HttpMethod::Get, 'path' => '/applications/{application_id}/emojis'],
                'getApplication' => ['method' => HttpMethod::Get, 'path' => '/applications/{application_id}/emojis/{emoji_id}'],
                'createApplication' => ['method' => HttpMethod::Post, 'path' => '/applications/{application_id}/emojis'],
                'editApplication' => ['method' => HttpMethod::Patch, 'path' => '/applications/{application_id}/emojis/{emoji_id}'],
                'deleteApplication' => ['method' => HttpMethod::Delete, 'path' => '/applications/{application_id}/emojis/{emoji_id}'],
            ],
            'entitlements' => [
                'list' => ['method' => HttpMethod::Get, 'path' => '/applications/{application_id}/entitlements'],
                'get' => ['method' => HttpMethod::Get, 'path' => '/applications/{application_id}/entitlements/{entitlement_id}'],
                'consume' => ['method' => HttpMethod::Post, 'path' => '/applications/{application_id}/entitlements/{entitlement_id}/consume'],
                'createTest' => ['method' => HttpMethod::Post, 'path' => '/applications/{application_id}/entitlements'],
                'deleteTest' => ['method' => HttpMethod::Delete, 'path' => '/applications/{application_id}/entitlements/{entitlement_id}'],
            ],
            'polls' => [
                'voters' => ['method' => HttpMethod::Get, 'path' => '/channels/{channel_id}/polls/{message_id}/answers/{answer_id}'],
                'end' => ['method' => HttpMethod::Post, 'path' => '/channels/{channel_id}/polls/{message_id}/expire'],
            ],
            'skus' => ['list' => ['method' => HttpMethod::Get, 'path' => '/applications/{application_id}/skus']],
            'subscriptions' => [
                'list' => ['method' => HttpMethod::Get, 'path' => '/skus/{sku_id}/subscriptions'],
                'get' => ['method' => HttpMethod::Get, 'path' => '/skus/{sku_id}/subscriptions/{subscription_id}'],
            ],
            'soundboards' => [
                'send' => ['method' => HttpMethod::Post, 'path' => '/channels/{channel_id}/send-soundboard-sound'],
                'defaults' => ['method' => HttpMethod::Get, 'path' => '/soundboard-default-sounds'],
                'listGuild' => ['method' => HttpMethod::Get, 'path' => '/guilds/{guild_id}/soundboard-sounds'],
                'getGuild' => ['method' => HttpMethod::Get, 'path' => '/guilds/{guild_id}/soundboard-sounds/{sound_id}'],
                'createGuild' => ['method' => HttpMethod::Post, 'path' => '/guilds/{guild_id}/soundboard-sounds'],
                'editGuild' => ['method' => HttpMethod::Patch, 'path' => '/guilds/{guild_id}/soundboard-sounds/{sound_id}'],
                'deleteGuild' => ['method' => HttpMethod::Delete, 'path' => '/guilds/{guild_id}/soundboard-sounds/{sound_id}'],
            ],
            'webhooks' => [
                'execute' => ['method' => HttpMethod::Post, 'path' => '/webhooks/{webhook_id}/{webhook_token}', 'auth' => AuthenticationRequirement::None],
                'getMessage' => ['method' => HttpMethod::Get, 'path' => '/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}', 'auth' => AuthenticationRequirement::None],
                'editMessage' => ['method' => HttpMethod::Patch, 'path' => '/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}', 'auth' => AuthenticationRequirement::None],
                'deleteMessage' => ['method' => HttpMethod::Delete, 'path' => '/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}', 'auth' => AuthenticationRequirement::None],
            ],
            'oauth2' => [
                'token' => ['method' => HttpMethod::Post, 'path' => '/oauth2/token', 'auth' => AuthenticationRequirement::None, 'form' => true],
                'revoke' => ['method' => HttpMethod::Post, 'path' => '/oauth2/token/revoke', 'auth' => AuthenticationRequirement::None, 'form' => true],
                'currentAuthorization' => ['method' => HttpMethod::Get, 'path' => '/oauth2/@me'],
            ],
            'interactions' => [
                'callback' => ['method' => HttpMethod::Post, 'path' => '/interactions/{interaction_id}/{interaction_token}/callback', 'auth' => AuthenticationRequirement::None],
            ],
            'lobbies' => [
                'create' => ['method' => HttpMethod::Post, 'path' => '/lobbies'],
                'createOrJoin' => ['method' => HttpMethod::Put, 'path' => '/lobbies'],
                'get' => ['method' => HttpMethod::Get, 'path' => '/lobbies/{lobby_id}'],
                'edit' => ['method' => HttpMethod::Patch, 'path' => '/lobbies/{lobby_id}'],
                'delete' => ['method' => HttpMethod::Delete, 'path' => '/lobbies/{lobby_id}'],
                'putMember' => ['method' => HttpMethod::Put, 'path' => '/lobbies/{lobby_id}/members/{user_id}'],
                'bulkMembers' => ['method' => HttpMethod::Post, 'path' => '/lobbies/{lobby_id}/members/bulk'],
                'deleteMember' => ['method' => HttpMethod::Delete, 'path' => '/lobbies/{lobby_id}/members/{user_id}'],
                'leave' => ['method' => HttpMethod::Delete, 'path' => '/lobbies/{lobby_id}/members/@me'],
                'linkChannel' => ['method' => HttpMethod::Patch, 'path' => '/lobbies/{lobby_id}/channel-linking'],
                'sendMessage' => ['method' => HttpMethod::Post, 'path' => '/lobbies/{lobby_id}/messages'],
                'listMessages' => ['method' => HttpMethod::Get, 'path' => '/lobbies/{lobby_id}/messages'],
                'moderateMessage' => ['method' => HttpMethod::Patch, 'path' => '/lobbies/{lobby_id}/messages/{message_id}/moderation'],
                'inviteSelf' => ['method' => HttpMethod::Post, 'path' => '/lobbies/{lobby_id}/members/@me/invites'],
                'inviteUser' => ['method' => HttpMethod::Post, 'path' => '/lobbies/{lobby_id}/members/{user_id}/invites'],
            ],
            'autoModeration' => [], 'auditLogs' => [], 'roleConnections' => [], 'scheduledEvents' => [], 'templates' => [], 'invites' => [], 'stages' => [], 'stickers' => [], 'users' => [], 'voice' => [],
        ];

        return array_replace_recursive(GeneratedEndpointDefinitions::all(), $modern);
    }

    public static function hasResource(string $name): bool
    {
        return array_key_exists($name, self::all());
    }

    /** @return array{method:HttpMethod,path:string,auth?:AuthenticationRequirement,form?:bool} */
    public static function endpoint(string $resource, string $endpoint): array
    {
        return self::all()[$resource][$endpoint] ?? throw new \InvalidArgumentException(sprintf('Unknown Discord endpoint %s.%s.', $resource, $endpoint));
    }

    /** @return array<string, array{method:HttpMethod,path:string}> */
    private static function crud(string $path): array
    {
        return [
            'get' => ['method' => HttpMethod::Get, 'path' => $path],
            'create' => ['method' => HttpMethod::Post, 'path' => $path],
            'edit' => ['method' => HttpMethod::Patch, 'path' => $path],
            'delete' => ['method' => HttpMethod::Delete, 'path' => $path],
        ];
    }
}
