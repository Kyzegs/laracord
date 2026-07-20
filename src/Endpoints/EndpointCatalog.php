<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Endpoints;

use Kyzegs\Laracord\Enums\AuthenticationRequirement;
use Kyzegs\Laracord\Enums\HttpMethod;

final class EndpointCatalog
{
    public const string AUDITED_AT = '2026-07-20';

    /**
     * Endpoint names are relative to their resource: the resource noun is dropped, so
     * `$client->guilds()->createChannel(...)` rather than `createGuildChannel`. Common
     * single-entity operations use `get`/`create`/`edit`/`delete` and collections use
     * `list`. When a resource is scoped by a qualifier (guild/application emojis,
     * global/guild commands, ...) the entity noun is kept so the name stays
     * unambiguous, e.g. `emojis()->listGuildEmojis()`. Every path is a real Discord
     * HTTP endpoint.
     *
     * @return array<string, array<string, array{method:HttpMethod,path:string,auth?:AuthenticationRequirement,form?:bool}>>
     */
    public static function all(): array
    {
        return [
            'applications' => [
                'getCurrent' => ['method' => HttpMethod::GET, 'path' => '/applications/@me'],
                'editCurrent' => ['method' => HttpMethod::PATCH, 'path' => '/applications/@me'],
                'getActivityInstance' => ['method' => HttpMethod::GET, 'path' => '/applications/{application_id}/activity-instances/{instance_id}'],
            ],
            'commands' => [
                'listGlobalCommands' => ['method' => HttpMethod::GET, 'path' => '/applications/{application_id}/commands'],
                'createGlobalCommand' => ['method' => HttpMethod::POST, 'path' => '/applications/{application_id}/commands'],
                'getGlobalCommand' => ['method' => HttpMethod::GET, 'path' => '/applications/{application_id}/commands/{command_id}'],
                'editGlobalCommand' => ['method' => HttpMethod::PATCH, 'path' => '/applications/{application_id}/commands/{command_id}'],
                'deleteGlobalCommand' => ['method' => HttpMethod::DELETE, 'path' => '/applications/{application_id}/commands/{command_id}'],
                'bulkOverwriteGlobalCommands' => ['method' => HttpMethod::PUT, 'path' => '/applications/{application_id}/commands'],
                'listGuildCommands' => ['method' => HttpMethod::GET, 'path' => '/applications/{application_id}/guilds/{guild_id}/commands'],
                'createGuildCommand' => ['method' => HttpMethod::POST, 'path' => '/applications/{application_id}/guilds/{guild_id}/commands'],
                'getGuildCommand' => ['method' => HttpMethod::GET, 'path' => '/applications/{application_id}/guilds/{guild_id}/commands/{command_id}'],
                'editGuildCommand' => ['method' => HttpMethod::PATCH, 'path' => '/applications/{application_id}/guilds/{guild_id}/commands/{command_id}'],
                'deleteGuildCommand' => ['method' => HttpMethod::DELETE, 'path' => '/applications/{application_id}/guilds/{guild_id}/commands/{command_id}'],
                'bulkOverwriteGuildCommands' => ['method' => HttpMethod::PUT, 'path' => '/applications/{application_id}/guilds/{guild_id}/commands'],
                'listGuildPermissions' => ['method' => HttpMethod::GET, 'path' => '/applications/{application_id}/guilds/{guild_id}/commands/permissions'],
                'getPermissions' => ['method' => HttpMethod::GET, 'path' => '/applications/{application_id}/guilds/{guild_id}/commands/{command_id}/permissions'],
                'editPermissions' => ['method' => HttpMethod::PUT, 'path' => '/applications/{application_id}/guilds/{guild_id}/commands/{command_id}/permissions'],
                'batchEditPermissions' => ['method' => HttpMethod::PUT, 'path' => '/applications/{application_id}/guilds/{guild_id}/commands/permissions'],
            ],
            'channels' => [
                'get' => ['method' => HttpMethod::GET, 'path' => '/channels/{channel_id}'],
                'edit' => ['method' => HttpMethod::PATCH, 'path' => '/channels/{channel_id}'],
                'delete' => ['method' => HttpMethod::DELETE, 'path' => '/channels/{channel_id}'],
                'editPermissions' => ['method' => HttpMethod::PUT, 'path' => '/channels/{channel_id}/permissions/{overwrite_id}'],
                'deletePermission' => ['method' => HttpMethod::DELETE, 'path' => '/channels/{channel_id}/permissions/{overwrite_id}'],
                'followAnnouncement' => ['method' => HttpMethod::POST, 'path' => '/channels/{channel_id}/followers'],
                'triggerTyping' => ['method' => HttpMethod::POST, 'path' => '/channels/{channel_id}/typing'],
                'listPinnedMessages' => ['method' => HttpMethod::GET, 'path' => '/channels/{channel_id}/pins'],
                'pinMessage' => ['method' => HttpMethod::PUT, 'path' => '/channels/{channel_id}/pins/{message_id}'],
                'unpinMessage' => ['method' => HttpMethod::DELETE, 'path' => '/channels/{channel_id}/pins/{message_id}'],
                'startThread' => ['method' => HttpMethod::POST, 'path' => '/channels/{channel_id}/threads'],
                'joinThread' => ['method' => HttpMethod::PUT, 'path' => '/channels/{channel_id}/thread-members/@me'],
                'addThreadMember' => ['method' => HttpMethod::PUT, 'path' => '/channels/{channel_id}/thread-members/{user_id}'],
                'leaveThread' => ['method' => HttpMethod::DELETE, 'path' => '/channels/{channel_id}/thread-members/@me'],
                'removeThreadMember' => ['method' => HttpMethod::DELETE, 'path' => '/channels/{channel_id}/thread-members/{user_id}'],
                'getThreadMember' => ['method' => HttpMethod::GET, 'path' => '/channels/{channel_id}/thread-members/{user_id}'],
                'listThreadMembers' => ['method' => HttpMethod::GET, 'path' => '/channels/{channel_id}/thread-members'],
                'listPublicArchivedThreads' => ['method' => HttpMethod::GET, 'path' => '/channels/{channel_id}/threads/archived/public'],
                'listPrivateArchivedThreads' => ['method' => HttpMethod::GET, 'path' => '/channels/{channel_id}/threads/archived/private'],
                'listJoinedPrivateArchivedThreads' => ['method' => HttpMethod::GET, 'path' => '/channels/{channel_id}/users/@me/threads/archived/private'],
            ],
            'messages' => [
                'list' => ['method' => HttpMethod::GET, 'path' => '/channels/{channel_id}/messages'],
                'get' => ['method' => HttpMethod::GET, 'path' => '/channels/{channel_id}/messages/{message_id}'],
                'create' => ['method' => HttpMethod::POST, 'path' => '/channels/{channel_id}/messages'],
                'edit' => ['method' => HttpMethod::PATCH, 'path' => '/channels/{channel_id}/messages/{message_id}'],
                'delete' => ['method' => HttpMethod::DELETE, 'path' => '/channels/{channel_id}/messages/{message_id}'],
                'crosspost' => ['method' => HttpMethod::POST, 'path' => '/channels/{channel_id}/messages/{message_id}/crosspost'],
                'createReaction' => ['method' => HttpMethod::PUT, 'path' => '/channels/{channel_id}/messages/{message_id}/reactions/{emoji}/@me'],
                'deleteOwnReaction' => ['method' => HttpMethod::DELETE, 'path' => '/channels/{channel_id}/messages/{message_id}/reactions/{emoji}/@me'],
                'deleteUserReaction' => ['method' => HttpMethod::DELETE, 'path' => '/channels/{channel_id}/messages/{message_id}/reactions/{emoji}/{user_id}'],
                'listReactions' => ['method' => HttpMethod::GET, 'path' => '/channels/{channel_id}/messages/{message_id}/reactions/{emoji}'],
                'deleteAllReactions' => ['method' => HttpMethod::DELETE, 'path' => '/channels/{channel_id}/messages/{message_id}/reactions'],
                'deleteAllReactionsForEmoji' => ['method' => HttpMethod::DELETE, 'path' => '/channels/{channel_id}/messages/{message_id}/reactions/{emoji}'],
                'bulkDelete' => ['method' => HttpMethod::POST, 'path' => '/channels/{channel_id}/messages/bulk-delete'],
                'startThread' => ['method' => HttpMethod::POST, 'path' => '/channels/{channel_id}/messages/{message_id}/threads'],
            ],
            'guilds' => [
                'create' => ['method' => HttpMethod::POST, 'path' => '/guilds'],
                'get' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}'],
                'getPreview' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/preview'],
                'edit' => ['method' => HttpMethod::PATCH, 'path' => '/guilds/{guild_id}'],
                'delete' => ['method' => HttpMethod::DELETE, 'path' => '/guilds/{guild_id}'],
                'listChannels' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/channels'],
                'createChannel' => ['method' => HttpMethod::POST, 'path' => '/guilds/{guild_id}/channels'],
                'modifyChannelPositions' => ['method' => HttpMethod::PATCH, 'path' => '/guilds/{guild_id}/channels'],
                'listActiveThreads' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/threads/active'],
                'getMember' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/members/{user_id}'],
                'listMembers' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/members'],
                'searchMembers' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/members/search'],
                'addMember' => ['method' => HttpMethod::PUT, 'path' => '/guilds/{guild_id}/members/{user_id}'],
                'modifyMember' => ['method' => HttpMethod::PATCH, 'path' => '/guilds/{guild_id}/members/{user_id}'],
                'modifyCurrentMember' => ['method' => HttpMethod::PATCH, 'path' => '/guilds/{guild_id}/members/@me'],
                'addMemberRole' => ['method' => HttpMethod::PUT, 'path' => '/guilds/{guild_id}/members/{user_id}/roles/{role_id}'],
                'removeMemberRole' => ['method' => HttpMethod::DELETE, 'path' => '/guilds/{guild_id}/members/{user_id}/roles/{role_id}'],
                'removeMember' => ['method' => HttpMethod::DELETE, 'path' => '/guilds/{guild_id}/members/{user_id}'],
                'listBans' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/bans'],
                'getBan' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/bans/{user_id}'],
                'createBan' => ['method' => HttpMethod::PUT, 'path' => '/guilds/{guild_id}/bans/{user_id}'],
                'removeBan' => ['method' => HttpMethod::DELETE, 'path' => '/guilds/{guild_id}/bans/{user_id}'],
                'listRoles' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/roles'],
                'createRole' => ['method' => HttpMethod::POST, 'path' => '/guilds/{guild_id}/roles'],
                'modifyRolePositions' => ['method' => HttpMethod::PATCH, 'path' => '/guilds/{guild_id}/roles'],
                'modifyRole' => ['method' => HttpMethod::PATCH, 'path' => '/guilds/{guild_id}/roles/{role_id}'],
                'modifyMfaLevel' => ['method' => HttpMethod::POST, 'path' => '/guilds/{guild_id}/mfa'],
                'deleteRole' => ['method' => HttpMethod::DELETE, 'path' => '/guilds/{guild_id}/roles/{role_id}'],
                'getPruneCount' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/prune'],
                'beginPrune' => ['method' => HttpMethod::POST, 'path' => '/guilds/{guild_id}/prune'],
                'listIntegrations' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/integrations'],
                'deleteIntegration' => ['method' => HttpMethod::DELETE, 'path' => '/guilds/{guild_id}/integrations/{integration_id}'],
                'getWidgetSettings' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/widget'],
                'modifyWidget' => ['method' => HttpMethod::PATCH, 'path' => '/guilds/{guild_id}/widget'],
                'getWidget' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/widget.json'],
                'getVanityUrl' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/vanity-url'],
                'getWidgetImage' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/widget.png'],
                'getWelcomeScreen' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/welcome-screen'],
                'modifyWelcomeScreen' => ['method' => HttpMethod::PATCH, 'path' => '/guilds/{guild_id}/welcome-screen'],
                'getOnboarding' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/onboarding'],
                'modifyOnboarding' => ['method' => HttpMethod::PATCH, 'path' => '/guilds/{guild_id}/onboarding'],
            ],
            'emojis' => [
                'listGuildEmojis' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/emojis'],
                'getGuildEmoji' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/emojis/{emoji_id}'],
                'createGuildEmoji' => ['method' => HttpMethod::POST, 'path' => '/guilds/{guild_id}/emojis'],
                'modifyGuildEmoji' => ['method' => HttpMethod::PATCH, 'path' => '/guilds/{guild_id}/emojis/{emoji_id}'],
                'deleteGuildEmoji' => ['method' => HttpMethod::DELETE, 'path' => '/guilds/{guild_id}/emojis/{emoji_id}'],
                'listApplicationEmojis' => ['method' => HttpMethod::GET, 'path' => '/applications/{application_id}/emojis'],
                'getApplicationEmoji' => ['method' => HttpMethod::GET, 'path' => '/applications/{application_id}/emojis/{emoji_id}'],
                'createApplicationEmoji' => ['method' => HttpMethod::POST, 'path' => '/applications/{application_id}/emojis'],
                'modifyApplicationEmoji' => ['method' => HttpMethod::PATCH, 'path' => '/applications/{application_id}/emojis/{emoji_id}'],
                'deleteApplicationEmoji' => ['method' => HttpMethod::DELETE, 'path' => '/applications/{application_id}/emojis/{emoji_id}'],
            ],
            'invites' => [
                'listChannelInvites' => ['method' => HttpMethod::GET, 'path' => '/channels/{channel_id}/invites'],
                'createChannelInvite' => ['method' => HttpMethod::POST, 'path' => '/channels/{channel_id}/invites'],
                'listGuildInvites' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/invites'],
                'get' => ['method' => HttpMethod::GET, 'path' => '/invites/{invite_code}'],
                'delete' => ['method' => HttpMethod::DELETE, 'path' => '/invites/{invite_code}'],
            ],
            'auditLogs' => [
                'get' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/audit-logs'],
            ],
            'autoModeration' => [
                'listRules' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/auto-moderation/rules'],
                'getRule' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/auto-moderation/rules/{auto_moderation_rule_id}'],
                'createRule' => ['method' => HttpMethod::POST, 'path' => '/guilds/{guild_id}/auto-moderation/rules'],
                'editRule' => ['method' => HttpMethod::PATCH, 'path' => '/guilds/{guild_id}/auto-moderation/rules/{auto_moderation_rule_id}'],
                'deleteRule' => ['method' => HttpMethod::DELETE, 'path' => '/guilds/{guild_id}/auto-moderation/rules/{auto_moderation_rule_id}'],
            ],
            'scheduledEvents' => [
                'list' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/events'],
                'create' => ['method' => HttpMethod::POST, 'path' => '/guilds/{guild_id}/events'],
                'get' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/events/{guild_scheduled_event_id}'],
                'edit' => ['method' => HttpMethod::PATCH, 'path' => '/guilds/{guild_id}/events/{guild_scheduled_event_id}'],
                'delete' => ['method' => HttpMethod::DELETE, 'path' => '/guilds/{guild_id}/events/{guild_scheduled_event_id}'],
                'listUsers' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/events/{guild_scheduled_event_id}/users'],
            ],
            'stages' => [
                'create' => ['method' => HttpMethod::POST, 'path' => '/stage-instances'],
                'get' => ['method' => HttpMethod::GET, 'path' => '/stage-instances/{channel_id}'],
                'edit' => ['method' => HttpMethod::PATCH, 'path' => '/stage-instances/{channel_id}'],
                'delete' => ['method' => HttpMethod::DELETE, 'path' => '/stage-instances/{channel_id}'],
            ],
            'stickers' => [
                'get' => ['method' => HttpMethod::GET, 'path' => '/stickers/{sticker_id}'],
                'listPacks' => ['method' => HttpMethod::GET, 'path' => '/sticker-packs'],
                'listGuildStickers' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/stickers'],
                'getGuildSticker' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/stickers/{sticker_id}'],
                'createGuildSticker' => ['method' => HttpMethod::POST, 'path' => '/guilds/{guild_id}/stickers'],
                'modifyGuildSticker' => ['method' => HttpMethod::PATCH, 'path' => '/guilds/{guild_id}/stickers/{sticker_id}'],
                'deleteGuildSticker' => ['method' => HttpMethod::DELETE, 'path' => '/guilds/{guild_id}/stickers/{sticker_id}'],
            ],
            'templates' => [
                'getGuildTemplate' => ['method' => HttpMethod::GET, 'path' => '/guilds/templates/{template_code}'],
                'createGuildFromTemplate' => ['method' => HttpMethod::POST, 'path' => '/guilds/templates/{template_code}'],
                'listGuildTemplates' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/templates'],
                'createGuildTemplate' => ['method' => HttpMethod::POST, 'path' => '/guilds/{guild_id}/templates'],
                'syncGuildTemplate' => ['method' => HttpMethod::PUT, 'path' => '/guilds/{guild_id}/templates/{template_code}'],
                'modifyGuildTemplate' => ['method' => HttpMethod::PATCH, 'path' => '/guilds/{guild_id}/templates/{template_code}'],
                'deleteGuildTemplate' => ['method' => HttpMethod::DELETE, 'path' => '/guilds/{guild_id}/templates/{template_code}'],
            ],
            'roleConnections' => [
                'getMetadata' => ['method' => HttpMethod::GET, 'path' => '/applications/{application_id}/role-connections/metadata'],
                'updateMetadata' => ['method' => HttpMethod::PUT, 'path' => '/applications/{application_id}/role-connections/metadata'],
                'getUserConnection' => ['method' => HttpMethod::GET, 'path' => '/users/@me/connections/{guild_id}'],
                'updateUserConnection' => ['method' => HttpMethod::PATCH, 'path' => '/users/@me/connections/{guild_id}'],
            ],
            'users' => [
                'getCurrent' => ['method' => HttpMethod::GET, 'path' => '/users/@me'],
                'get' => ['method' => HttpMethod::GET, 'path' => '/users/{user_id}'],
                'modifyCurrent' => ['method' => HttpMethod::PATCH, 'path' => '/users/@me'],
                'listGuilds' => ['method' => HttpMethod::GET, 'path' => '/users/@me/guilds'],
                'getGuildMember' => ['method' => HttpMethod::GET, 'path' => '/users/@me/guilds/{guild_id}/member'],
                'leaveGuild' => ['method' => HttpMethod::DELETE, 'path' => '/users/@me/guilds/{guild_id}'],
                'createDm' => ['method' => HttpMethod::POST, 'path' => '/users/@me/channels'],
                'listConnections' => ['method' => HttpMethod::GET, 'path' => '/users/@me/connections'],
            ],
            'voice' => [
                'listGuildRegions' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/regions'],
                'modifyCurrentState' => ['method' => HttpMethod::PATCH, 'path' => '/guilds/{guild_id}/voice-states/@me'],
                'modifyState' => ['method' => HttpMethod::PATCH, 'path' => '/guilds/{guild_id}/voice-states/{user_id}'],
                'listRegions' => ['method' => HttpMethod::GET, 'path' => '/voice/regions'],
            ],
            'webhooks' => [
                'create' => ['method' => HttpMethod::POST, 'path' => '/channels/{channel_id}/webhooks'],
                'listChannelWebhooks' => ['method' => HttpMethod::GET, 'path' => '/channels/{channel_id}/webhooks'],
                'listGuildWebhooks' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/webhooks'],
                'get' => ['method' => HttpMethod::GET, 'path' => '/webhooks/{webhook_id}'],
                'getWithToken' => ['method' => HttpMethod::GET, 'path' => '/webhooks/{webhook_id}/{webhook_token}', 'auth' => AuthenticationRequirement::NONE],
                'edit' => ['method' => HttpMethod::PATCH, 'path' => '/webhooks/{webhook_id}'],
                'editWithToken' => ['method' => HttpMethod::PATCH, 'path' => '/webhooks/{webhook_id}/{webhook_token}', 'auth' => AuthenticationRequirement::NONE],
                'delete' => ['method' => HttpMethod::DELETE, 'path' => '/webhooks/{webhook_id}'],
                'deleteWithToken' => ['method' => HttpMethod::DELETE, 'path' => '/webhooks/{webhook_id}/{webhook_token}', 'auth' => AuthenticationRequirement::NONE],
                'execute' => ['method' => HttpMethod::POST, 'path' => '/webhooks/{webhook_id}/{webhook_token}', 'auth' => AuthenticationRequirement::NONE],
                'executeSlack' => ['method' => HttpMethod::POST, 'path' => '/webhooks/{webhook_id}/{webhook_token}/slack', 'auth' => AuthenticationRequirement::NONE],
                'executeGitHub' => ['method' => HttpMethod::POST, 'path' => '/webhooks/{webhook_id}/{webhook_token}/github', 'auth' => AuthenticationRequirement::NONE],
                'getMessage' => ['method' => HttpMethod::GET, 'path' => '/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}', 'auth' => AuthenticationRequirement::NONE],
                'editMessage' => ['method' => HttpMethod::PATCH, 'path' => '/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}', 'auth' => AuthenticationRequirement::NONE],
                'deleteMessage' => ['method' => HttpMethod::DELETE, 'path' => '/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}', 'auth' => AuthenticationRequirement::NONE],
            ],
            'entitlements' => [
                'list' => ['method' => HttpMethod::GET, 'path' => '/applications/{application_id}/entitlements'],
                'get' => ['method' => HttpMethod::GET, 'path' => '/applications/{application_id}/entitlements/{entitlement_id}'],
                'consume' => ['method' => HttpMethod::POST, 'path' => '/applications/{application_id}/entitlements/{entitlement_id}/consume'],
                'createTest' => ['method' => HttpMethod::POST, 'path' => '/applications/{application_id}/entitlements'],
                'deleteTest' => ['method' => HttpMethod::DELETE, 'path' => '/applications/{application_id}/entitlements/{entitlement_id}'],
            ],
            'polls' => [
                'listVoters' => ['method' => HttpMethod::GET, 'path' => '/channels/{channel_id}/polls/{message_id}/answers/{answer_id}'],
                'end' => ['method' => HttpMethod::POST, 'path' => '/channels/{channel_id}/polls/{message_id}/expire'],
            ],
            'skus' => [
                'list' => ['method' => HttpMethod::GET, 'path' => '/applications/{application_id}/skus'],
            ],
            'subscriptions' => [
                'list' => ['method' => HttpMethod::GET, 'path' => '/skus/{sku_id}/subscriptions'],
                'get' => ['method' => HttpMethod::GET, 'path' => '/skus/{sku_id}/subscriptions/{subscription_id}'],
            ],
            'soundboards' => [
                'send' => ['method' => HttpMethod::POST, 'path' => '/channels/{channel_id}/send-soundboard-sound'],
                'listDefault' => ['method' => HttpMethod::GET, 'path' => '/soundboard-default-sounds'],
                'listGuildSounds' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/soundboard-sounds'],
                'getGuildSound' => ['method' => HttpMethod::GET, 'path' => '/guilds/{guild_id}/soundboard-sounds/{sound_id}'],
                'createGuildSound' => ['method' => HttpMethod::POST, 'path' => '/guilds/{guild_id}/soundboard-sounds'],
                'modifyGuildSound' => ['method' => HttpMethod::PATCH, 'path' => '/guilds/{guild_id}/soundboard-sounds/{sound_id}'],
                'deleteGuildSound' => ['method' => HttpMethod::DELETE, 'path' => '/guilds/{guild_id}/soundboard-sounds/{sound_id}'],
            ],
            'oauth2' => [
                'token' => ['method' => HttpMethod::POST, 'path' => '/oauth2/token', 'auth' => AuthenticationRequirement::NONE, 'form' => true],
                'revoke' => ['method' => HttpMethod::POST, 'path' => '/oauth2/token/revoke', 'auth' => AuthenticationRequirement::NONE, 'form' => true],
                'getCurrentAuthorization' => ['method' => HttpMethod::GET, 'path' => '/oauth2/@me'],
            ],
            'interactions' => [
                'callback' => ['method' => HttpMethod::POST, 'path' => '/interactions/{interaction_id}/{interaction_token}/callback', 'auth' => AuthenticationRequirement::NONE],
                'getOriginal' => ['method' => HttpMethod::GET, 'path' => '/webhooks/{application_id}/{interaction_token}/messages/@original', 'auth' => AuthenticationRequirement::NONE],
                'editOriginal' => ['method' => HttpMethod::PATCH, 'path' => '/webhooks/{application_id}/{interaction_token}/messages/@original', 'auth' => AuthenticationRequirement::NONE],
                'deleteOriginal' => ['method' => HttpMethod::DELETE, 'path' => '/webhooks/{application_id}/{interaction_token}/messages/@original', 'auth' => AuthenticationRequirement::NONE],
                'createFollowup' => ['method' => HttpMethod::POST, 'path' => '/webhooks/{application_id}/{interaction_token}', 'auth' => AuthenticationRequirement::NONE],
                'getFollowup' => ['method' => HttpMethod::GET, 'path' => '/webhooks/{application_id}/{interaction_token}/messages/{message_id}', 'auth' => AuthenticationRequirement::NONE],
                'editFollowup' => ['method' => HttpMethod::PATCH, 'path' => '/webhooks/{application_id}/{interaction_token}/messages/{message_id}', 'auth' => AuthenticationRequirement::NONE],
                'deleteFollowup' => ['method' => HttpMethod::DELETE, 'path' => '/webhooks/{application_id}/{interaction_token}/messages/{message_id}', 'auth' => AuthenticationRequirement::NONE],
            ],
            'lobbies' => [
                'create' => ['method' => HttpMethod::POST, 'path' => '/lobbies'],
                'createOrJoin' => ['method' => HttpMethod::PUT, 'path' => '/lobbies'],
                'get' => ['method' => HttpMethod::GET, 'path' => '/lobbies/{lobby_id}'],
                'edit' => ['method' => HttpMethod::PATCH, 'path' => '/lobbies/{lobby_id}'],
                'delete' => ['method' => HttpMethod::DELETE, 'path' => '/lobbies/{lobby_id}'],
                'addMember' => ['method' => HttpMethod::PUT, 'path' => '/lobbies/{lobby_id}/members/{user_id}'],
                'bulkMembers' => ['method' => HttpMethod::POST, 'path' => '/lobbies/{lobby_id}/members/bulk'],
                'removeMember' => ['method' => HttpMethod::DELETE, 'path' => '/lobbies/{lobby_id}/members/{user_id}'],
                'leave' => ['method' => HttpMethod::DELETE, 'path' => '/lobbies/{lobby_id}/members/@me'],
                'linkChannel' => ['method' => HttpMethod::PATCH, 'path' => '/lobbies/{lobby_id}/channel-linking'],
                'sendMessage' => ['method' => HttpMethod::POST, 'path' => '/lobbies/{lobby_id}/messages'],
                'listMessages' => ['method' => HttpMethod::GET, 'path' => '/lobbies/{lobby_id}/messages'],
                'moderateMessage' => ['method' => HttpMethod::PATCH, 'path' => '/lobbies/{lobby_id}/messages/{message_id}/moderation'],
                'inviteSelf' => ['method' => HttpMethod::POST, 'path' => '/lobbies/{lobby_id}/members/@me/invites'],
                'inviteUser' => ['method' => HttpMethod::POST, 'path' => '/lobbies/{lobby_id}/members/{user_id}/invites'],
            ],
        ];
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
}
