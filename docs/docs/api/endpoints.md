# Endpoint Catalog

Audited against Discord HTTP documentation on 2026-06-23.

Call any entry with `$client->{resource}()->call($endpoint, $parameters, $body, $query, $files, $reason)`.

## Applications

| Endpoint | Method | Path |
|---|---:|---|
| `getCurrentApplication` | `GET` | `/applications/@me` |
| `getCurrent` | `GET` | `/applications/@me` |
| `editCurrent` | `PATCH` | `/applications/@me` |
| `getActivityInstance` | `GET` | `/applications/{application_id}/activity-instances/{instance_id}` |

## Audit Logs

| Endpoint | Method | Path |
|---|---:|---|
| `getGuildAuditLog` | `GET` | `/guilds/{guild_id}/audit-logs` |

## Auto Moderation

| Endpoint | Method | Path |
|---|---:|---|
| `listAutoModerationRulesForGuild` | `GET` | `/guilds/{guild_id}/auto-moderation/rules` |
| `getAutoModerationRule` | `GET` | `/guilds/{guild_id}/auto-moderation/rules/{auto_moderation_rule_id}` |
| `createAutoModerationRule` | `POST` | `/guilds/{guild_id}/auto-moderation/rules` |
| `modifyAutoModerationRule` | `PATCH` | `/guilds/{guild_id}/auto-moderation/rules/{auto_moderation_rule_id}` |
| `deleteAutoModerationRule` | `DELETE` | `/guilds/{guild_id}/auto-moderation/rules/{auto_moderation_rule_id}` |

## Channels

| Endpoint | Method | Path |
|---|---:|---|
| `getChannel` | `GET` | `/channels/{channel_id}` |
| `modifyChannel` | `PATCH` | `/channels/{channel_id}` |
| `deleteChannel` | `DELETE` | `/channels/{channel_id}` |
| `editChannelPermissions` | `PUT` | `/channels/{channel_id}/permissions/{overwrite_id}` |
| `deleteChannelPermission` | `DELETE` | `/channels/{channel_id}/permissions/{overwrite_id}` |
| `followAnnouncementChannel` | `POST` | `/channels/{channel_id}/followers` |
| `triggerTypingIndicator` | `POST` | `/channels/{channel_id}/typing` |
| `getPinnedMessages` | `GET` | `/channels/{channel_id}/pins` |
| `pinMessage` | `PUT` | `/channels/{channel_id}/pins/{message_id}` |
| `unpinMessage` | `DELETE` | `/channels/{channel_id}/pins/{message_id}` |
| `startThreadWithoutMessage` | `POST` | `/channels/{channel_id}/threads` |
| `startThreadInForumChannel` | `POST` | `/channels/{channel_id}/threads` |
| `joinThread` | `PUT` | `/channels/{channel_id}/thread-members/@me` |
| `addThreadMember` | `PUT` | `/channels/{channel_id}/thread-members/{user_id}` |
| `leaveThread` | `DELETE` | `/channels/{channel_id}/thread-members/@me` |
| `removeThreadMember` | `DELETE` | `/channels/{channel_id}/thread-members/{user_id}` |
| `getThreadMember` | `GET` | `/channels/{channel_id}/thread-members/{user_id}` |
| `listThreadMembers` | `GET` | `/channels/{channel_id}/thread-members` |
| `listPublicArchivedThreads` | `GET` | `/channels/{channel_id}/threads/archived/public` |
| `listPrivateArchivedThreads` | `GET` | `/channels/{channel_id}/threads/archived/private` |
| `listJoinedPrivateArchivedThreads` | `GET` | `/channels/{channel_id}/users/@me/threads/archived/private` |
| `get` | `GET` | `/channels/{channel_id}` |
| `create` | `POST` | `/channels/{channel_id}` |
| `edit` | `PATCH` | `/channels/{channel_id}` |
| `delete` | `DELETE` | `/channels/{channel_id}` |

## Commands

| Endpoint | Method | Path |
|---|---:|---|
| `getGlobalApplicationCommands` | `GET` | `/applications/{application_id}/commands` |
| `createGlobalApplicationCommand` | `POST` | `/applications/{application_id}/commands` |
| `getGlobalApplicationCommand` | `GET` | `/applications/{application_id}/commands/{command_id}` |
| `editGlobalApplicationCommand` | `PATCH` | `/applications/{application_id}/commands/{command_id}` |
| `deleteGlobalApplicationCommand` | `DELETE` | `/applications/{application_id}/commands/{command_id}` |
| `bulkOverwriteGlobalApplicationCommands` | `PUT` | `/applications/{application_id}/commands` |
| `getGuildApplicationCommands` | `GET` | `/applications/{application_id}/guilds/{guild_id}/commands` |
| `createGuildApplicationCommand` | `POST` | `/applications/{application_id}/guilds/{guild_id}/commands` |
| `getGuildApplicationCommand` | `GET` | `/applications/{application_id}/guilds/{guild_id}/commands/{command_id}` |
| `editGuildApplicationCommand` | `PATCH` | `/applications/{application_id}/guilds/{guild_id}/commands/{command_id}` |
| `deleteGuildApplicationCommand` | `DELETE` | `/applications/{application_id}/guilds/{guild_id}/commands/{command_id}` |
| `bulkOverwriteGuildApplicationCommands` | `PUT` | `/applications/{application_id}/guilds/{guild_id}/commands` |
| `getGuildApplicationCommandPermissions` | `GET` | `/applications/{application_id}/guilds/{guild_id}/commands/permissions` |
| `getApplicationCommandPermissions` | `GET` | `/applications/{application_id}/guilds/{guild_id}/commands/{command_id}/permissions` |
| `editApplicationCommandPermissions` | `PUT` | `/applications/{application_id}/guilds/{guild_id}/commands/{command_id}/permissions` |
| `batchEditApplicationCommandPermissions` | `PUT` | `/applications/{application_id}/guilds/{guild_id}/commands/permissions` |
| `listGlobal` | `GET` | `/applications/{application_id}/commands` |
| `createGlobal` | `POST` | `/applications/{application_id}/commands` |
| `listGuild` | `GET` | `/applications/{application_id}/guilds/{guild_id}/commands` |
| `createGuild` | `POST` | `/applications/{application_id}/guilds/{guild_id}/commands` |

## Emojis

| Endpoint | Method | Path |
|---|---:|---|
| `listGuildEmojis` | `GET` | `/guilds/{guild_id}/emojis` |
| `getGuildEmoji` | `GET` | `/guilds/{guild_id}/emojis/{emoji_id}` |
| `createGuildEmoji` | `POST` | `/guilds/{guild_id}/emojis` |
| `modifyGuildEmoji` | `PATCH` | `/guilds/{guild_id}/emojis/{emoji_id}` |
| `deleteGuildEmoji` | `DELETE` | `/guilds/{guild_id}/emojis/{emoji_id}` |
| `get` | `GET` | `/guilds/{guild_id}/emojis/{emoji_id}` |
| `create` | `POST` | `/guilds/{guild_id}/emojis/{emoji_id}` |
| `edit` | `PATCH` | `/guilds/{guild_id}/emojis/{emoji_id}` |
| `delete` | `DELETE` | `/guilds/{guild_id}/emojis/{emoji_id}` |
| `listApplication` | `GET` | `/applications/{application_id}/emojis` |
| `getApplication` | `GET` | `/applications/{application_id}/emojis/{emoji_id}` |
| `createApplication` | `POST` | `/applications/{application_id}/emojis` |
| `editApplication` | `PATCH` | `/applications/{application_id}/emojis/{emoji_id}` |
| `deleteApplication` | `DELETE` | `/applications/{application_id}/emojis/{emoji_id}` |

## Guilds

| Endpoint | Method | Path |
|---|---:|---|
| `createGuild` | `POST` | `/guilds` |
| `getGuild` | `GET` | `/guilds/{guild_id}` |
| `getGuildPreview` | `GET` | `/guilds/{guild_id}/preview` |
| `modifyGuild` | `PATCH` | `/guilds/{guild_id}` |
| `deleteGuild` | `DELETE` | `/guilds/{guild_id}` |
| `getGuildChannels` | `GET` | `/guilds/{guild_id}/channels` |
| `createGuildChannel` | `POST` | `/guilds/{guild_id}/channels` |
| `modifyGuildChannelPositions` | `PATCH` | `/guilds/{guild_id}/channels` |
| `listActiveGuildThreads` | `GET` | `/guilds/{guild_id}/threads/active` |
| `getGuildMember` | `GET` | `/guilds/{guild_id}/members/{user_id}` |
| `listGuildMembers` | `GET` | `/guilds/{guild_id}/members` |
| `searchGuildMembers` | `GET` | `/guilds/{guild_id}/members/search` |
| `addGuildMember` | `PUT` | `/guilds/{guild_id}/members/{user_id}` |
| `modifyGuildMember` | `PATCH` | `/guilds/{guild_id}/members/{user_id}` |
| `modifyCurrentMember` | `PATCH` | `/guilds/{guild_id}/members/@me` |
| `addGuildMemberRole` | `PUT` | `/guilds/{guild_id}/members/{user_id}/roles/{role_id}` |
| `removeGuildMemberRole` | `DELETE` | `/guilds/{guild_id}/members/{user_id}/roles/{role_id}` |
| `removeGuildMember` | `DELETE` | `/guilds/{guild_id}/members/{user_id}` |
| `getGuildBans` | `GET` | `/guilds/{guild_id}/bans` |
| `getGuildBan` | `GET` | `/guilds/{guild_id}/bans/{user_id}` |
| `createGuildBan` | `PUT` | `/guilds/{guild_id}/bans/{user_id}` |
| `removeGuildBan` | `DELETE` | `/guilds/{guild_id}/bans/{user_id}` |
| `getGuildRoles` | `GET` | `/guilds/{guild_id}/roles` |
| `createGuildRole` | `POST` | `/guilds/{guild_id}/roles` |
| `modifyGuildRolePositions` | `PATCH` | `/guilds/{guild_id}/roles` |
| `modifyGuildRole` | `PATCH` | `/guilds/{guild_id}/roles/{role_id}` |
| `modifyGuildMfaLevel` | `POST` | `/guilds/{guild_id}/mfa` |
| `deleteGuildRole` | `DELETE` | `/guilds/{guild_id}/roles/{role_id}` |
| `getGuildPruneCount` | `GET` | `/guilds/{guild_id}/prune` |
| `beginGuildPrune` | `POST` | `/guilds/{guild_id}/prune` |
| `getGuildIntegrations` | `GET` | `/guilds/{guild_id}/integrations` |
| `deleteGuildIntegrations` | `DELETE` | `/guilds/{guild_id}/integrations/{integration_id}` |
| `getGuildWidgetSettings` | `GET` | `/guilds/{guild_id}/widget` |
| `modifyGuildWidget` | `PATCH` | `/guilds/{guild_id}/widget` |
| `getGuildWidget` | `GET` | `/guilds/{guild_id}/widget.json` |
| `getGuildVanityUrl` | `GET` | `/guilds/{guild_id}/vanity-url` |
| `getGuildWidgetImage` | `GET` | `/guilds/{guild_id}/widget.png` |
| `getGuildWelcomeScreen` | `GET` | `/guilds/{guild_id}/welcome-screen` |
| `modifyGuildWelcomeScreen` | `PATCH` | `/guilds/{guild_id}/welcome-screen` |
| `getGuildOnboarding` | `GET` | `/guilds/{guild_id}/onboarding` |
| `modifyGuildOnboarding` | `PATCH` | `/guilds/{guild_id}/onboarding` |
| `get` | `GET` | `/guilds/{guild_id}` |
| `create` | `POST` | `/guilds/{guild_id}` |
| `edit` | `PATCH` | `/guilds/{guild_id}` |
| `delete` | `DELETE` | `/guilds/{guild_id}` |
| `listMembers` | `GET` | `/guilds/{guild_id}/members` |
| `listChannels` | `GET` | `/guilds/{guild_id}/channels` |

## Invites

| Endpoint | Method | Path |
|---|---:|---|
| `getChannelInvites` | `GET` | `/channels/{channel_id}/invites` |
| `createChannelInvite` | `POST` | `/channels/{channel_id}/invites` |
| `getGuildInvites` | `GET` | `/guilds/{guild_id}/invites` |
| `getInvite` | `GET` | `/invites/{invite_code}` |
| `deleteInvite` | `DELETE` | `/invites/{invite_code}` |

## Messages

| Endpoint | Method | Path |
|---|---:|---|
| `getChannelMessages` | `GET` | `/channels/{channel_id}/messages` |
| `getChannelMessage` | `GET` | `/channels/{channel_id}/messages/{message_id}` |
| `createMessage` | `POST` | `/channels/{channel_id}/messages` |
| `crosspostMessage` | `POST` | `/channels/{channel_id}/messages/{message_id}/crosspost` |
| `createReaction` | `PUT` | `/channels/{channel_id}/messages/{message_id}/reactions/{emoji}/@me` |
| `deleteOwnReaction` | `DELETE` | `/channels/{channel_id}/messages/{message_id}/reactions/{emoji}/@me` |
| `deleteUserReaction` | `DELETE` | `/channels/{channel_id}/messages/{message_id}/reactions/{emoji}/{user_id}` |
| `getReactions` | `GET` | `/channels/{channel_id}/messages/{message_id}/reactions/{emoji}` |
| `deleteAllReactions` | `DELETE` | `/channels/{channel_id}/messages/{message_id}/reactions` |
| `deleteAllReactionsForEmoji` | `DELETE` | `/channels/{channel_id}/messages/{message_id}/reactions/{emoji}` |
| `editMessage` | `PATCH` | `/channels/{channel_id}/messages/{message_id}` |
| `deleteMessage` | `DELETE` | `/channels/{channel_id}/messages/{message_id}` |
| `bulkDeleteMessages` | `POST` | `/channels/{channel_id}/messages/bulk-delete` |
| `startThreadFromMessage` | `POST` | `/channels/{channel_id}/messages/{message_id}/threads` |
| `getWebhookMessage` | `GET` | `/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}` |
| `editWebhookMessage` | `PATCH` | `/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}` |
| `deleteWebhookMessage` | `DELETE` | `/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}` |
| `list` | `GET` | `/channels/{channel_id}/messages` |
| `get` | `GET` | `/channels/{channel_id}/messages/{message_id}` |
| `create` | `POST` | `/channels/{channel_id}/messages` |
| `edit` | `PATCH` | `/channels/{channel_id}/messages/{message_id}` |
| `delete` | `DELETE` | `/channels/{channel_id}/messages/{message_id}` |

## Role Connections

| Endpoint | Method | Path |
|---|---:|---|
| `getApplicationRoleConnectionMetadataRecords` | `GET` | `/applications/{application_id}/role-connections/metadata` |
| `updateApplicationRoleConnectionMetadataRecords` | `PUT` | `/applications/{application_id}/role-connections/metadata` |
| `getUserApplicationRoleConnections` | `GET` | `/users/@me/connections/{guild_id}` |
| `updateUserApplicationRoleConnections` | `PATCH` | `/users/@me/connections/{guild_id}` |

## Scheduled Events

| Endpoint | Method | Path |
|---|---:|---|
| `listScheduledEventsForGuild` | `GET` | `/guilds/{guild_id}/events` |
| `createGuildScheduledEvent` | `POST` | `/guilds/{guild_id}/events` |
| `getGuildScheduledEvent` | `GET` | `/guilds/{guild_id}/events/{guild_scheduled_event_id}` |
| `modifyGuildScheduledEvent` | `PATCH` | `/guilds/{guild_id}/events/{guild_scheduled_event_id}` |
| `deleteGuildScheduledEvent` | `DELETE` | `/guilds/{guild_id}/events/{guild_scheduled_event_id}` |
| `getGuildScheduledEventUsers` | `GET` | `/guilds/{guild_id}/events/{guild_scheduled_event_id}/users` |

## Stages

| Endpoint | Method | Path |
|---|---:|---|
| `createStageInstance` | `POST` | `/stage-instances` |
| `getStageInstance` | `GET` | `/stage-instances/{channel_id}` |
| `modifyStageInstance` | `PATCH` | `/stage-instances/{channel_id}` |
| `deleteStageInstance` | `DELETE` | `/stage-instances/{channel_id}` |

## Stickers

| Endpoint | Method | Path |
|---|---:|---|
| `getSticker` | `GET` | `/stickers/{sticker_id}` |
| `listNitroStickerPacks` | `GET` | `/sticker-packs` |
| `listGuildStickers` | `GET` | `/guilds/{guild_id}/stickers` |
| `getGuildStickers` | `GET` | `/guilds/{guild_id}/stickers` |
| `getGuildSticker` | `GET` | `/guilds/{guild_id}/stickers/{sticker_id}` |
| `createGuildSticker` | `POST` | `/guilds/{guild_id}/stickers` |
| `modifyGuildSticker` | `PATCH` | `/guilds/{guild_id}/stickers/{sticker_id}` |
| `deleteGuildSticker` | `DELETE` | `/guilds/{guild_id}/stickers/{sticker_id}` |

## Templates

| Endpoint | Method | Path |
|---|---:|---|
| `getGuildTemplate` | `GET` | `/guilds/templates/{template_code}` |
| `createGuildFromTemplate` | `POST` | `/guilds/templates/{template_code}` |
| `getGuildTemplates` | `GET` | `/guilds/{guild_id}/templates` |
| `createGuildTemplate` | `POST` | `/guilds/{guild_id}/templates` |
| `syncGuildTemplate` | `PUT` | `/guilds/{guild_id}/templates/{template_code}` |
| `modifyGuildTemplate` | `PATCH` | `/guilds/{guild_id}/templates/{template_code}` |
| `deleteGuildTemplate` | `DELETE` | `/guilds/{guild_id}/templates/{template_code}` |

## Users

| Endpoint | Method | Path |
|---|---:|---|
| `getCurrentUser` | `GET` | `/users/@me` |
| `getUser` | `GET` | `/users/{user_id}` |
| `modifyCurrentUser` | `PATCH` | `/users/@me` |
| `getCurrentUserGuilds` | `GET` | `/users/@me/guilds` |
| `getCurrentUserGuildMember` | `GET` | `/users/@me/guilds/{guild_id}/member` |
| `leaveGuild` | `DELETE` | `/users/@me/guilds/{guild_id}` |
| `createDm` | `POST` | `/users/@me/channels` |
| `createGroupDm` | `POST` | `/users/@me/channels` |
| `getUserConnections` | `GET` | `/users/@me/connections` |

## Voice

| Endpoint | Method | Path |
|---|---:|---|
| `getGuildVoiceRegions` | `GET` | `/guilds/{guild_id}/regions` |
| `modifyCurrentUserVoiceState` | `PATCH` | `/guilds/{guild_id}/voice-states/@me` |
| `modifyUserVoiceState` | `PATCH` | `/guilds/{guild_id}/voice-states/{user_id}` |
| `listVoiceRegions` | `GET` | `/voice/regions` |

## Webhooks

| Endpoint | Method | Path |
|---|---:|---|
| `createWebhook` | `POST` | `/channels/{channel_id}/webhooks` |
| `getChannelWebhooks` | `GET` | `/channels/{channel_id}/webhooks` |
| `getGuildWebhooks` | `GET` | `/guilds/{guild_id}/webhooks` |
| `getWebhook` | `GET` | `/webhooks/{webhook_id}` |
| `getWebhookWithToken` | `GET` | `/webhooks/{webhook_id}/{webhook_token}` |
| `modifyWebhook` | `PATCH` | `/webhooks/{webhook_id}` |
| `modifyWebhookWithToken` | `PATCH` | `/webhooks/{webhook_id}/{webhook_token}` |
| `deleteWebhook` | `DELETE` | `/webhooks/{webhook_id}` |
| `deleteWebhookWithToken` | `DELETE` | `/webhooks/{webhook_id}/{webhook_token}` |
| `executeWebhook` | `POST` | `/webhooks/{webhook_id}/{webhook_token}` |
| `executeSlackCompatibleWebhook` | `POST` | `/webhooks/{webhook_id}/{webhook_token}/slack` |
| `executeGitHubCompatibleWebhook` | `POST` | `/webhooks/{webhook_id}/{webhook_token}/github` |
| `execute` | `POST` | `/webhooks/{webhook_id}/{webhook_token}` |
| `getMessage` | `GET` | `/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}` |
| `editMessage` | `PATCH` | `/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}` |
| `deleteMessage` | `DELETE` | `/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}` |

## Entitlements

| Endpoint | Method | Path |
|---|---:|---|
| `list` | `GET` | `/applications/{application_id}/entitlements` |
| `get` | `GET` | `/applications/{application_id}/entitlements/{entitlement_id}` |
| `consume` | `POST` | `/applications/{application_id}/entitlements/{entitlement_id}/consume` |
| `createTest` | `POST` | `/applications/{application_id}/entitlements` |
| `deleteTest` | `DELETE` | `/applications/{application_id}/entitlements/{entitlement_id}` |

## Polls

| Endpoint | Method | Path |
|---|---:|---|
| `voters` | `GET` | `/channels/{channel_id}/polls/{message_id}/answers/{answer_id}` |
| `end` | `POST` | `/channels/{channel_id}/polls/{message_id}/expire` |

## Skus

| Endpoint | Method | Path |
|---|---:|---|
| `list` | `GET` | `/applications/{application_id}/skus` |

## Subscriptions

| Endpoint | Method | Path |
|---|---:|---|
| `list` | `GET` | `/skus/{sku_id}/subscriptions` |
| `get` | `GET` | `/skus/{sku_id}/subscriptions/{subscription_id}` |

## Soundboards

| Endpoint | Method | Path |
|---|---:|---|
| `send` | `POST` | `/channels/{channel_id}/send-soundboard-sound` |
| `defaults` | `GET` | `/soundboard-default-sounds` |
| `listGuild` | `GET` | `/guilds/{guild_id}/soundboard-sounds` |
| `getGuild` | `GET` | `/guilds/{guild_id}/soundboard-sounds/{sound_id}` |
| `createGuild` | `POST` | `/guilds/{guild_id}/soundboard-sounds` |
| `editGuild` | `PATCH` | `/guilds/{guild_id}/soundboard-sounds/{sound_id}` |
| `deleteGuild` | `DELETE` | `/guilds/{guild_id}/soundboard-sounds/{sound_id}` |

## Oauth2

| Endpoint | Method | Path |
|---|---:|---|
| `token` | `POST` | `/oauth2/token` |
| `revoke` | `POST` | `/oauth2/token/revoke` |
| `currentAuthorization` | `GET` | `/oauth2/@me` |

## Interactions

| Endpoint | Method | Path |
|---|---:|---|
| `callback` | `POST` | `/interactions/{interaction_id}/{interaction_token}/callback` |

## Lobbies

| Endpoint | Method | Path |
|---|---:|---|
| `create` | `POST` | `/lobbies` |
| `createOrJoin` | `PUT` | `/lobbies` |
| `get` | `GET` | `/lobbies/{lobby_id}` |
| `edit` | `PATCH` | `/lobbies/{lobby_id}` |
| `delete` | `DELETE` | `/lobbies/{lobby_id}` |
| `putMember` | `PUT` | `/lobbies/{lobby_id}/members/{user_id}` |
| `bulkMembers` | `POST` | `/lobbies/{lobby_id}/members/bulk` |
| `deleteMember` | `DELETE` | `/lobbies/{lobby_id}/members/{user_id}` |
| `leave` | `DELETE` | `/lobbies/{lobby_id}/members/@me` |
| `linkChannel` | `PATCH` | `/lobbies/{lobby_id}/channel-linking` |
| `sendMessage` | `POST` | `/lobbies/{lobby_id}/messages` |
| `listMessages` | `GET` | `/lobbies/{lobby_id}/messages` |
| `moderateMessage` | `PATCH` | `/lobbies/{lobby_id}/messages/{message_id}/moderation` |
| `inviteSelf` | `POST` | `/lobbies/{lobby_id}/members/@me/invites` |
| `inviteUser` | `POST` | `/lobbies/{lobby_id}/members/{user_id}/invites` |
