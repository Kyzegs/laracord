# Endpoint Catalog

Call any entry with `$client->{resource}()->call($endpoint, $parameters, $body, $query, $files, $reason)`.

## Applications

| Endpoint | Method | Path |
|---|---:|---|
| `get` | `GET` | `/applications/{application_id}` |
| `edit` | `PATCH` | `/applications/{application_id}` |
| `getCurrent` | `GET` | `/applications/@me` |
| `editCurrent` | `PATCH` | `/applications/@me` |
| `uploadAttachment` | `POST` | `/applications/{application_id}/attachment` |
| `getActivityInstance` | `GET` | `/applications/{application_id}/activity-instances/{instance_id}` |

## Commands

| Endpoint | Method | Path |
|---|---:|---|
| `listGlobalCommands` | `GET` | `/applications/{application_id}/commands` |
| `createGlobalCommand` | `POST` | `/applications/{application_id}/commands` |
| `getGlobalCommand` | `GET` | `/applications/{application_id}/commands/{command_id}` |
| `editGlobalCommand` | `PATCH` | `/applications/{application_id}/commands/{command_id}` |
| `deleteGlobalCommand` | `DELETE` | `/applications/{application_id}/commands/{command_id}` |
| `bulkOverwriteGlobalCommands` | `PUT` | `/applications/{application_id}/commands` |
| `listGuildCommands` | `GET` | `/applications/{application_id}/guilds/{guild_id}/commands` |
| `createGuildCommand` | `POST` | `/applications/{application_id}/guilds/{guild_id}/commands` |
| `getGuildCommand` | `GET` | `/applications/{application_id}/guilds/{guild_id}/commands/{command_id}` |
| `editGuildCommand` | `PATCH` | `/applications/{application_id}/guilds/{guild_id}/commands/{command_id}` |
| `deleteGuildCommand` | `DELETE` | `/applications/{application_id}/guilds/{guild_id}/commands/{command_id}` |
| `bulkOverwriteGuildCommands` | `PUT` | `/applications/{application_id}/guilds/{guild_id}/commands` |
| `listGuildPermissions` | `GET` | `/applications/{application_id}/guilds/{guild_id}/commands/permissions` |
| `getPermissions` | `GET` | `/applications/{application_id}/guilds/{guild_id}/commands/{command_id}/permissions` |
| `editPermissions` | `PUT` | `/applications/{application_id}/guilds/{guild_id}/commands/{command_id}/permissions` |

## Channels

| Endpoint | Method | Path |
|---|---:|---|
| `get` | `GET` | `/channels/{channel_id}` |
| `edit` | `PATCH` | `/channels/{channel_id}` |
| `delete` | `DELETE` | `/channels/{channel_id}` |
| `editPermissions` | `PUT` | `/channels/{channel_id}/permissions/{overwrite_id}` |
| `deletePermission` | `DELETE` | `/channels/{channel_id}/permissions/{overwrite_id}` |
| `followAnnouncement` | `POST` | `/channels/{channel_id}/followers` |
| `triggerTyping` | `POST` | `/channels/{channel_id}/typing` |
| `getMemberPermissions` | `GET` | `/channels/{channel_id}/permissions/member/{user_id}` |
| `getRolePermissions` | `GET` | `/channels/{channel_id}/permissions/role/{role_id}` |
| `addRecipient` | `PUT` | `/channels/{channel_id}/recipients/{user_id}` |
| `removeRecipient` | `DELETE` | `/channels/{channel_id}/recipients/{user_id}` |
| `setVoiceStatus` | `PUT` | `/channels/{channel_id}/voice-status` |
| `listPinnedMessages` | `GET` | `/channels/{channel_id}/messages/pins` |
| `pinMessage` | `PUT` | `/channels/{channel_id}/messages/pins/{message_id}` |
| `unpinMessage` | `DELETE` | `/channels/{channel_id}/messages/pins/{message_id}` |
| `searchThreads` | `GET` | `/channels/{channel_id}/threads/search` |
| `startThread` | `POST` | `/channels/{channel_id}/threads` |
| `joinThread` | `PUT` | `/channels/{channel_id}/thread-members/@me` |
| `addThreadMember` | `PUT` | `/channels/{channel_id}/thread-members/{user_id}` |
| `leaveThread` | `DELETE` | `/channels/{channel_id}/thread-members/@me` |
| `removeThreadMember` | `DELETE` | `/channels/{channel_id}/thread-members/{user_id}` |
| `getThreadMember` | `GET` | `/channels/{channel_id}/thread-members/{user_id}` |
| `listThreadMembers` | `GET` | `/channels/{channel_id}/thread-members` |
| `listPublicArchivedThreads` | `GET` | `/channels/{channel_id}/threads/archived/public` |
| `listPrivateArchivedThreads` | `GET` | `/channels/{channel_id}/threads/archived/private` |
| `listJoinedPrivateArchivedThreads` | `GET` | `/channels/{channel_id}/users/@me/threads/archived/private` |

## Messages

| Endpoint | Method | Path |
|---|---:|---|
| `list` | `GET` | `/channels/{channel_id}/messages` |
| `get` | `GET` | `/channels/{channel_id}/messages/{message_id}` |
| `create` | `POST` | `/channels/{channel_id}/messages` |
| `edit` | `PATCH` | `/channels/{channel_id}/messages/{message_id}` |
| `delete` | `DELETE` | `/channels/{channel_id}/messages/{message_id}` |
| `crosspost` | `POST` | `/channels/{channel_id}/messages/{message_id}/crosspost` |
| `createReaction` | `PUT` | `/channels/{channel_id}/messages/{message_id}/reactions/{emoji}/@me` |
| `deleteOwnReaction` | `DELETE` | `/channels/{channel_id}/messages/{message_id}/reactions/{emoji}/@me` |
| `deleteUserReaction` | `DELETE` | `/channels/{channel_id}/messages/{message_id}/reactions/{emoji}/{user_id}` |
| `listReactions` | `GET` | `/channels/{channel_id}/messages/{message_id}/reactions/{emoji}` |
| `deleteAllReactions` | `DELETE` | `/channels/{channel_id}/messages/{message_id}/reactions` |
| `deleteAllReactionsForEmoji` | `DELETE` | `/channels/{channel_id}/messages/{message_id}/reactions/{emoji}` |
| `bulkDelete` | `POST` | `/channels/{channel_id}/messages/bulk-delete` |
| `startThread` | `POST` | `/channels/{channel_id}/messages/{message_id}/threads` |

## Guilds

| Endpoint | Method | Path |
|---|---:|---|
| `get` | `GET` | `/guilds/{guild_id}` |
| `getPreview` | `GET` | `/guilds/{guild_id}/preview` |
| `edit` | `PATCH` | `/guilds/{guild_id}` |
| `getMemberPermissions` | `GET` | `/guilds/{guild_id}/permissions/member/{user_id}` |
| `getRolePermissions` | `GET` | `/guilds/{guild_id}/permissions/role/{role_id}` |
| `searchMessages` | `GET` | `/guilds/{guild_id}/messages/search` |
| `listJoinRequests` | `GET` | `/guilds/{guild_id}/requests` |
| `editJoinRequest` | `PATCH` | `/guilds/{guild_id}/requests/{request_id}` |
| `getNewMemberWelcome` | `GET` | `/guilds/{guild_id}/new-member-welcome` |
| `editIncidentActions` | `PUT` | `/guilds/{guild_id}/incident-actions` |
| `listChannels` | `GET` | `/guilds/{guild_id}/channels` |
| `createChannel` | `POST` | `/guilds/{guild_id}/channels` |
| `modifyChannelPositions` | `PATCH` | `/guilds/{guild_id}/channels` |
| `listActiveThreads` | `GET` | `/guilds/{guild_id}/threads/active` |
| `getMember` | `GET` | `/guilds/{guild_id}/members/{user_id}` |
| `listMembers` | `GET` | `/guilds/{guild_id}/members` |
| `searchMembers` | `GET` | `/guilds/{guild_id}/members/search` |
| `addMember` | `PUT` | `/guilds/{guild_id}/members/{user_id}` |
| `modifyMember` | `PATCH` | `/guilds/{guild_id}/members/{user_id}` |
| `modifyCurrentMember` | `PATCH` | `/guilds/{guild_id}/members/@me` |
| `addMemberRole` | `PUT` | `/guilds/{guild_id}/members/{user_id}/roles/{role_id}` |
| `removeMemberRole` | `DELETE` | `/guilds/{guild_id}/members/{user_id}/roles/{role_id}` |
| `removeMember` | `DELETE` | `/guilds/{guild_id}/members/{user_id}` |
| `listBans` | `GET` | `/guilds/{guild_id}/bans` |
| `getBan` | `GET` | `/guilds/{guild_id}/bans/{user_id}` |
| `createBan` | `PUT` | `/guilds/{guild_id}/bans/{user_id}` |
| `removeBan` | `DELETE` | `/guilds/{guild_id}/bans/{user_id}` |
| `bulkBan` | `POST` | `/guilds/{guild_id}/bulk-ban` |
| `listRoles` | `GET` | `/guilds/{guild_id}/roles` |
| `getRole` | `GET` | `/guilds/{guild_id}/roles/{role_id}` |
| `getRoleMemberCounts` | `GET` | `/guilds/{guild_id}/roles/member-counts` |
| `createRole` | `POST` | `/guilds/{guild_id}/roles` |
| `modifyRolePositions` | `PATCH` | `/guilds/{guild_id}/roles` |
| `modifyRole` | `PATCH` | `/guilds/{guild_id}/roles/{role_id}` |
| `deleteRole` | `DELETE` | `/guilds/{guild_id}/roles/{role_id}` |
| `getPruneCount` | `GET` | `/guilds/{guild_id}/prune` |
| `beginPrune` | `POST` | `/guilds/{guild_id}/prune` |
| `listIntegrations` | `GET` | `/guilds/{guild_id}/integrations` |
| `deleteIntegration` | `DELETE` | `/guilds/{guild_id}/integrations/{integration_id}` |
| `getWidgetSettings` | `GET` | `/guilds/{guild_id}/widget` |
| `modifyWidget` | `PATCH` | `/guilds/{guild_id}/widget` |
| `getWidget` | `GET` | `/guilds/{guild_id}/widget.json` |
| `getVanityUrl` | `GET` | `/guilds/{guild_id}/vanity-url` |
| `getWidgetImage` | `GET` | `/guilds/{guild_id}/widget.png` |
| `getWelcomeScreen` | `GET` | `/guilds/{guild_id}/welcome-screen` |
| `modifyWelcomeScreen` | `PATCH` | `/guilds/{guild_id}/welcome-screen` |
| `getOnboarding` | `GET` | `/guilds/{guild_id}/onboarding` |
| `modifyOnboarding` | `PUT` | `/guilds/{guild_id}/onboarding` |

## Emojis

| Endpoint | Method | Path |
|---|---:|---|
| `listGuildEmojis` | `GET` | `/guilds/{guild_id}/emojis` |
| `getGuildEmoji` | `GET` | `/guilds/{guild_id}/emojis/{emoji_id}` |
| `createGuildEmoji` | `POST` | `/guilds/{guild_id}/emojis` |
| `modifyGuildEmoji` | `PATCH` | `/guilds/{guild_id}/emojis/{emoji_id}` |
| `deleteGuildEmoji` | `DELETE` | `/guilds/{guild_id}/emojis/{emoji_id}` |
| `listApplicationEmojis` | `GET` | `/applications/{application_id}/emojis` |
| `getApplicationEmoji` | `GET` | `/applications/{application_id}/emojis/{emoji_id}` |
| `createApplicationEmoji` | `POST` | `/applications/{application_id}/emojis` |
| `modifyApplicationEmoji` | `PATCH` | `/applications/{application_id}/emojis/{emoji_id}` |
| `deleteApplicationEmoji` | `DELETE` | `/applications/{application_id}/emojis/{emoji_id}` |

## Invites

| Endpoint | Method | Path |
|---|---:|---|
| `listChannelInvites` | `GET` | `/channels/{channel_id}/invites` |
| `createChannelInvite` | `POST` | `/channels/{channel_id}/invites` |
| `listGuildInvites` | `GET` | `/guilds/{guild_id}/invites` |
| `get` | `GET` | `/invites/{invite_code}` |
| `delete` | `DELETE` | `/invites/{invite_code}` |
| `listTargetUsers` | `GET` | `/invites/{invite_code}/target-users` |
| `editTargetUsers` | `PUT` | `/invites/{invite_code}/target-users` |
| `getTargetUsersJobStatus` | `GET` | `/invites/{invite_code}/target-users/job-status` |
| `addTargetUser` | `PUT` | `/invites/{invite_code}/target-users/{user_id}` |
| `removeTargetUser` | `DELETE` | `/invites/{invite_code}/target-users/{user_id}` |
| `bulkAddTargetUsers` | `POST` | `/invites/{invite_code}/target-users/bulk-add` |
| `bulkRemoveTargetUsers` | `POST` | `/invites/{invite_code}/target-users/bulk-delete` |

## Audit Logs

| Endpoint | Method | Path |
|---|---:|---|
| `get` | `GET` | `/guilds/{guild_id}/audit-logs` |

## Auto Moderation

| Endpoint | Method | Path |
|---|---:|---|
| `listRules` | `GET` | `/guilds/{guild_id}/auto-moderation/rules` |
| `getRule` | `GET` | `/guilds/{guild_id}/auto-moderation/rules/{auto_moderation_rule_id}` |
| `createRule` | `POST` | `/guilds/{guild_id}/auto-moderation/rules` |
| `editRule` | `PATCH` | `/guilds/{guild_id}/auto-moderation/rules/{auto_moderation_rule_id}` |
| `deleteRule` | `DELETE` | `/guilds/{guild_id}/auto-moderation/rules/{auto_moderation_rule_id}` |

## Scheduled Events

| Endpoint | Method | Path |
|---|---:|---|
| `list` | `GET` | `/guilds/{guild_id}/scheduled-events` |
| `create` | `POST` | `/guilds/{guild_id}/scheduled-events` |
| `get` | `GET` | `/guilds/{guild_id}/scheduled-events/{guild_scheduled_event_id}` |
| `edit` | `PATCH` | `/guilds/{guild_id}/scheduled-events/{guild_scheduled_event_id}` |
| `delete` | `DELETE` | `/guilds/{guild_id}/scheduled-events/{guild_scheduled_event_id}` |
| `listUsers` | `GET` | `/guilds/{guild_id}/scheduled-events/{guild_scheduled_event_id}/users` |
| `countUsers` | `GET` | `/guilds/{guild_id}/scheduled-events/{guild_scheduled_event_id}/users/counts` |
| `listExceptionUsers` | `GET` | `/guilds/{guild_id}/scheduled-events/{guild_scheduled_event_id}/{exception_id}/users` |
| `createException` | `POST` | `/guilds/{guild_id}/scheduled-events/{guild_scheduled_event_id}/exceptions` |
| `editException` | `PATCH` | `/guilds/{guild_id}/scheduled-events/{guild_scheduled_event_id}/exceptions/{exception_id}` |
| `deleteException` | `DELETE` | `/guilds/{guild_id}/scheduled-events/{guild_scheduled_event_id}/exceptions/{exception_id}` |

## Stages

| Endpoint | Method | Path |
|---|---:|---|
| `create` | `POST` | `/stage-instances` |
| `get` | `GET` | `/stage-instances/{channel_id}` |
| `edit` | `PATCH` | `/stage-instances/{channel_id}` |
| `delete` | `DELETE` | `/stage-instances/{channel_id}` |

## Stickers

| Endpoint | Method | Path |
|---|---:|---|
| `get` | `GET` | `/stickers/{sticker_id}` |
| `listPacks` | `GET` | `/sticker-packs` |
| `getPack` | `GET` | `/sticker-packs/{pack_id}` |
| `listGuildStickers` | `GET` | `/guilds/{guild_id}/stickers` |
| `getGuildSticker` | `GET` | `/guilds/{guild_id}/stickers/{sticker_id}` |
| `createGuildSticker` | `POST` | `/guilds/{guild_id}/stickers` |
| `modifyGuildSticker` | `PATCH` | `/guilds/{guild_id}/stickers/{sticker_id}` |
| `deleteGuildSticker` | `DELETE` | `/guilds/{guild_id}/stickers/{sticker_id}` |

## Templates

| Endpoint | Method | Path |
|---|---:|---|
| `getGuildTemplate` | `GET` | `/guilds/templates/{template_code}` |
| `listGuildTemplates` | `GET` | `/guilds/{guild_id}/templates` |
| `createGuildTemplate` | `POST` | `/guilds/{guild_id}/templates` |
| `syncGuildTemplate` | `PUT` | `/guilds/{guild_id}/templates/{template_code}` |
| `modifyGuildTemplate` | `PATCH` | `/guilds/{guild_id}/templates/{template_code}` |
| `deleteGuildTemplate` | `DELETE` | `/guilds/{guild_id}/templates/{template_code}` |

## Role Connections

| Endpoint | Method | Path |
|---|---:|---|
| `getMetadata` | `GET` | `/applications/{application_id}/role-connections/metadata` |
| `updateMetadata` | `PUT` | `/applications/{application_id}/role-connections/metadata` |
| `getUserConnection` | `GET` | `/users/@me/applications/{application_id}/role-connection` |
| `updateUserConnection` | `PUT` | `/users/@me/applications/{application_id}/role-connection` |
| `deleteUserConnection` | `DELETE` | `/users/@me/applications/{application_id}/role-connection` |

## Users

| Endpoint | Method | Path |
|---|---:|---|
| `getCurrent` | `GET` | `/users/@me` |
| `get` | `GET` | `/users/{user_id}` |
| `modifyCurrent` | `PATCH` | `/users/@me` |
| `listGuilds` | `GET` | `/users/@me/guilds` |
| `getGuildMember` | `GET` | `/users/@me/guilds/{guild_id}/member` |
| `leaveGuild` | `DELETE` | `/users/@me/guilds/{guild_id}` |
| `createDm` | `POST` | `/users/@me/channels` |
| `listConnections` | `GET` | `/users/@me/connections` |

## Voice

| Endpoint | Method | Path |
|---|---:|---|
| `listGuildRegions` | `GET` | `/guilds/{guild_id}/regions` |
| `getCurrentState` | `GET` | `/guilds/{guild_id}/voice-states/@me` |
| `getState` | `GET` | `/guilds/{guild_id}/voice-states/{user_id}` |
| `modifyCurrentState` | `PATCH` | `/guilds/{guild_id}/voice-states/@me` |
| `modifyState` | `PATCH` | `/guilds/{guild_id}/voice-states/{user_id}` |
| `listRegions` | `GET` | `/voice/regions` |

## Webhooks

| Endpoint | Method | Path |
|---|---:|---|
| `create` | `POST` | `/channels/{channel_id}/webhooks` |
| `listChannelWebhooks` | `GET` | `/channels/{channel_id}/webhooks` |
| `listGuildWebhooks` | `GET` | `/guilds/{guild_id}/webhooks` |
| `get` | `GET` | `/webhooks/{webhook_id}` |
| `getWithToken` | `GET` | `/webhooks/{webhook_id}/{webhook_token}` |
| `edit` | `PATCH` | `/webhooks/{webhook_id}` |
| `editWithToken` | `PATCH` | `/webhooks/{webhook_id}/{webhook_token}` |
| `delete` | `DELETE` | `/webhooks/{webhook_id}` |
| `deleteWithToken` | `DELETE` | `/webhooks/{webhook_id}/{webhook_token}` |
| `execute` | `POST` | `/webhooks/{webhook_id}/{webhook_token}` |
| `executeSlack` | `POST` | `/webhooks/{webhook_id}/{webhook_token}/slack` |
| `executeGitHub` | `POST` | `/webhooks/{webhook_id}/{webhook_token}/github` |
| `getMessage` | `GET` | `/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}` |
| `editMessage` | `PATCH` | `/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}` |
| `deleteMessage` | `DELETE` | `/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}` |

## Entitlements

| Endpoint | Method | Path |
|---|---:|---|
| `list` | `GET` | `/applications/{application_id}/entitlements` |
| `listForCurrentUser` | `GET` | `/users/@me/applications/{application_id}/entitlements` |
| `get` | `GET` | `/applications/{application_id}/entitlements/{entitlement_id}` |
| `consume` | `POST` | `/applications/{application_id}/entitlements/{entitlement_id}/consume` |
| `createTest` | `POST` | `/applications/{application_id}/entitlements` |
| `deleteTest` | `DELETE` | `/applications/{application_id}/entitlements/{entitlement_id}` |

## Polls

| Endpoint | Method | Path |
|---|---:|---|
| `listVoters` | `GET` | `/channels/{channel_id}/polls/{message_id}/answers/{answer_id}` |
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
| `listDefault` | `GET` | `/soundboard-default-sounds` |
| `listGuildSounds` | `GET` | `/guilds/{guild_id}/soundboard-sounds` |
| `getGuildSound` | `GET` | `/guilds/{guild_id}/soundboard-sounds/{sound_id}` |
| `createGuildSound` | `POST` | `/guilds/{guild_id}/soundboard-sounds` |
| `modifyGuildSound` | `PATCH` | `/guilds/{guild_id}/soundboard-sounds/{sound_id}` |
| `deleteGuildSound` | `DELETE` | `/guilds/{guild_id}/soundboard-sounds/{sound_id}` |

## Oauth2

| Endpoint | Method | Path |
|---|---:|---|
| `token` | `POST` | `/oauth2/token` |
| `revoke` | `POST` | `/oauth2/token/revoke` |
| `getCurrentAuthorization` | `GET` | `/oauth2/@me` |
| `getCurrentApplication` | `GET` | `/oauth2/applications/@me` |
| `getPublicKeys` | `GET` | `/oauth2/keys` |
| `getUserInfo` | `GET` | `/oauth2/userinfo` |

## Interactions

| Endpoint | Method | Path |
|---|---:|---|
| `callback` | `POST` | `/interactions/{interaction_id}/{interaction_token}/callback` |
| `getOriginal` | `GET` | `/webhooks/{application_id}/{interaction_token}/messages/@original` |
| `editOriginal` | `PATCH` | `/webhooks/{application_id}/{interaction_token}/messages/@original` |
| `deleteOriginal` | `DELETE` | `/webhooks/{application_id}/{interaction_token}/messages/@original` |
| `createFollowup` | `POST` | `/webhooks/{application_id}/{interaction_token}` |
| `getFollowup` | `GET` | `/webhooks/{application_id}/{interaction_token}/messages/{message_id}` |
| `editFollowup` | `PATCH` | `/webhooks/{application_id}/{interaction_token}/messages/{message_id}` |
| `deleteFollowup` | `DELETE` | `/webhooks/{application_id}/{interaction_token}/messages/{message_id}` |

## Lobbies

| Endpoint | Method | Path |
|---|---:|---|
| `create` | `POST` | `/lobbies` |
| `createOrJoin` | `PUT` | `/lobbies` |
| `get` | `GET` | `/lobbies/{lobby_id}` |
| `edit` | `PATCH` | `/lobbies/{lobby_id}` |
| `delete` | `DELETE` | `/lobbies/{lobby_id}` |
| `addMember` | `PUT` | `/lobbies/{lobby_id}/members/{user_id}` |
| `bulkMembers` | `POST` | `/lobbies/{lobby_id}/members/bulk` |
| `removeMember` | `DELETE` | `/lobbies/{lobby_id}/members/{user_id}` |
| `leave` | `DELETE` | `/lobbies/{lobby_id}/members/@me` |
| `linkChannel` | `PATCH` | `/lobbies/{lobby_id}/channel-linking` |
| `sendMessage` | `POST` | `/lobbies/{lobby_id}/messages` |
| `listMessages` | `GET` | `/lobbies/{lobby_id}/messages` |
| `moderateMessage` | `PUT` | `/lobbies/{lobby_id}/messages/{message_id}/moderation-metadata` |
| `inviteSelf` | `POST` | `/lobbies/{lobby_id}/members/@me/invites` |
| `inviteUser` | `POST` | `/lobbies/{lobby_id}/members/{user_id}/invites` |

## Gateway

| Endpoint | Method | Path |
|---|---:|---|
| `get` | `GET` | `/gateway` |
| `getBot` | `GET` | `/gateway/bot` |

## Partner Sdk

| Endpoint | Method | Path |
|---|---:|---|
| `token` | `POST` | `/partner-sdk/token` |
| `botToken` | `POST` | `/partner-sdk/token/bot` |
| `unmergeProvisionalAccount` | `POST` | `/partner-sdk/provisional-accounts/unmerge` |
| `botUnmergeProvisionalAccount` | `POST` | `/partner-sdk/provisional-accounts/unmerge/bot` |
| `moderateDmMessage` | `PUT` | `/partner-sdk/dms/{user_id_1}/{user_id_2}/messages/{message_id}/moderation-metadata` |

## Application Identities

| Endpoint | Method | Path |
|---|---:|---|
| `getProfile` | `GET` | `/applications/{application_id}/users/{user_id}/identities/{provider_issued_user_id}/profile` |
| `editProfile` | `PATCH` | `/applications/{application_id}/users/{user_id}/identities/{provider_issued_user_id}/profile` |
| `listForUser` | `GET` | `/users/{user_id}/application-identities/{application_id}` |
| `getByProviderUser` | `GET` | `/applications/{application_id}/application-identities/{provider_type}/{provider_issued_user_id}` |
| `delete` | `POST` | `/users/{user_id}/application-identities/{application_id}/{provider_type}/{provider_issued_user_id}/delete` |

