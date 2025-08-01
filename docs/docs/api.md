# API Reference

Welcome to the Laracord API reference. This documentation covers all available methods in the Laracord Facade.

## Quick Start

```php
use Kyzegs\Laracord\Facades\Laracord;

// Get a channel
$channel = Laracord::getChannel(123456789);

// Create a message
$message = Laracord::createMessage(123456789, [
    'content' => 'Hello, Discord!'
]);
```

## Method Categories

- [Application Commands](./api/application-commands.md) (16 methods)
- [Channels & Messages](./api/channels.md) (28 methods)
- [Guilds](./api/guilds.md) (69 methods)
- [Users](./api/users.md) (9 methods)
- [Webhooks](./api/webhooks.md) (10 methods)

## All Methods

- [`getGlobalApplicationCommands`](./api/application-commands.md#getGlobalApplicationCommands) - Retrieve all global application commands for an application.
- [`createGlobalApplicationCommand`](./api/application-commands.md#createGlobalApplicationCommand) - Create global application command
- [`getGlobalApplicationCommand`](./api/application-commands.md#getGlobalApplicationCommand) - Get global application command
- [`editGlobalApplicationCommand`](./api/application-commands.md#editGlobalApplicationCommand) - Edit global application command
- [`deleteGlobalApplicationCommand`](./api/application-commands.md#deleteGlobalApplicationCommand) - Delete global application command
- [`bulkOverwriteGlobalApplicationCommands`](./api/application-commands.md#bulkOverwriteGlobalApplicationCommands) - Bulk overwrite global application commands
- [`getGuildApplicationCommands`](./api/application-commands.md#getGuildApplicationCommands) - Get guild application commands
- [`createGuildApplicationCommand`](./api/application-commands.md#createGuildApplicationCommand) - Create guild application command
- [`getGuildApplicationCommand`](./api/application-commands.md#getGuildApplicationCommand) - Get guild application command
- [`editGuildApplicationCommand`](./api/application-commands.md#editGuildApplicationCommand) - Edit guild application command
- [`deleteGuildApplicationCommand`](./api/application-commands.md#deleteGuildApplicationCommand) - Delete guild application command
- [`bulkOverwriteGuildApplicationCommands`](./api/application-commands.md#bulkOverwriteGuildApplicationCommands) - Bulk overwrite guild application commands
- [`getGuildApplicationCommandPermissions`](./api/application-commands.md#getGuildApplicationCommandPermissions) - Get guild application command permissions
- [`getApplicationCommandPermissions`](./api/application-commands.md#getApplicationCommandPermissions) - Get application command permissions
- [`editApplicationCommandPermissions`](./api/application-commands.md#editApplicationCommandPermissions) - Edit application command permissions
- [`batchEditApplicationCommandPermissions`](./api/application-commands.md#batchEditApplicationCommandPermissions) - Batch edit application command permissions
- [`getCurrentApplication`](./api/general.md#getCurrentApplication) - Get current application
- [`getApplicationRoleConnectionMetadataRecords`](./api/general.md#getApplicationRoleConnectionMetadataRecords) - Get application role connection metadata records
- [`updateApplicationRoleConnectionMetadataRecords`](./api/general.md#updateApplicationRoleConnectionMetadataRecords) - Update application role connection metadata records
- [`getGuildAuditLog`](./api/guilds.md#getGuildAuditLog) - Get guild audit log
- [`listAutoModerationRulesForGuild`](./api/guilds.md#listAutoModerationRulesForGuild) - List auto moderation rules for guild
- [`getAutoModerationRule`](./api/general.md#getAutoModerationRule) - Get auto moderation rule
- [`createAutoModerationRule`](./api/general.md#createAutoModerationRule) - Create auto moderation rule
- [`modifyAutoModerationRule`](./api/general.md#modifyAutoModerationRule) - Modify auto moderation rule
- [`deleteAutoModerationRule`](./api/general.md#deleteAutoModerationRule) - Delete auto moderation rule
- [`getChannel`](./api/channels.md#getChannel) - Get channel
- [`modifyChannel`](./api/channels.md#modifyChannel) - Modify channel
- [`deleteChannel`](./api/channels.md#deleteChannel) - Delete channel
- [`getChannelMessages`](./api/channels.md#getChannelMessages) - Get channel messages
- [`getChannelMessage`](./api/channels.md#getChannelMessage) - Get channel message
- [`createMessage`](./api/channels.md#createMessage) - Create message
- [`crosspostMessage`](./api/channels.md#crosspostMessage) - Crosspost message
- [`createReaction`](./api/general.md#createReaction) - Create reaction
- [`deleteOwnReaction`](./api/general.md#deleteOwnReaction) - Delete own reaction
- [`deleteUserReaction`](./api/users.md#deleteUserReaction) - Delete user reaction
- [`getReactions`](./api/general.md#getReactions) - Get reactions
- [`deleteAllReactions`](./api/general.md#deleteAllReactions) - Delete all reactions
- [`deleteAllReactionsForEmoji`](./api/general.md#deleteAllReactionsForEmoji) - Delete all reactions for emoji
- [`editMessage`](./api/channels.md#editMessage) - Edit message
- [`deleteMessage`](./api/channels.md#deleteMessage) - Delete message
- [`bulkDeleteMessages`](./api/channels.md#bulkDeleteMessages) - Bulk delete messages
- [`editChannelPermissions`](./api/channels.md#editChannelPermissions) - Edit channel permissions
- [`getChannelInvites`](./api/channels.md#getChannelInvites) - Get channel invites
- [`createChannelInvite`](./api/channels.md#createChannelInvite) - Create channel invite
- [`deleteChannelPermission`](./api/channels.md#deleteChannelPermission) - Delete channel permission
- [`followAnnouncementChannel`](./api/channels.md#followAnnouncementChannel) - Follow announcement channel
- [`triggerTypingIndicator`](./api/general.md#triggerTypingIndicator) - Trigger typing indicator
- [`getPinnedMessages`](./api/channels.md#getPinnedMessages) - Get pinned messages
- [`pinMessage`](./api/channels.md#pinMessage) - Pin message
- [`unpinMessage`](./api/channels.md#unpinMessage) - Unpin message
- [`startThreadFromMessage`](./api/channels.md#startThreadFromMessage) - Start thread from message
- [`startThreadWithoutMessage`](./api/channels.md#startThreadWithoutMessage) - Start thread without message
- [`startThreadInForumChannel`](./api/channels.md#startThreadInForumChannel) - Start thread in forum channel
- [`joinThread`](./api/general.md#joinThread) - Join thread
- [`addThreadMember`](./api/general.md#addThreadMember) - Add thread member
- [`leaveThread`](./api/general.md#leaveThread) - Leave thread
- [`removeThreadMember`](./api/general.md#removeThreadMember) - Remove thread member
- [`getThreadMember`](./api/general.md#getThreadMember) - Get thread member
- [`listThreadMembers`](./api/general.md#listThreadMembers) - List thread members
- [`listPublicArchivedThreads`](./api/general.md#listPublicArchivedThreads) - List public archived threads
- [`listPrivateArchivedThreads`](./api/general.md#listPrivateArchivedThreads) - List private archived threads
- [`listJoinedPrivateArchivedThreads`](./api/general.md#listJoinedPrivateArchivedThreads) - List joined private archived threads
- [`listGuildEmojis`](./api/guilds.md#listGuildEmojis) - List guild emojis
- [`getGuildEmoji`](./api/guilds.md#getGuildEmoji) - Get guild emoji
- [`createGuildEmoji`](./api/guilds.md#createGuildEmoji) - Create guild emoji
- [`modifyGuildEmoji`](./api/guilds.md#modifyGuildEmoji) - Modify guild emoji
- [`deleteGuildEmoji`](./api/guilds.md#deleteGuildEmoji) - Delete guild emoji
- [`createGuild`](./api/guilds.md#createGuild) - Create guild
- [`getGuild`](./api/guilds.md#getGuild) - Get guild
- [`getGuildPreview`](./api/guilds.md#getGuildPreview) - Get guild preview
- [`modifyGuild`](./api/guilds.md#modifyGuild) - Modify guild
- [`deleteGuild`](./api/guilds.md#deleteGuild) - Delete guild
- [`getGuildChannels`](./api/channels.md#getGuildChannels) - Get guild channels
- [`createGuildChannel`](./api/channels.md#createGuildChannel) - Create guild channel
- [`modifyGuildChannelPositions`](./api/channels.md#modifyGuildChannelPositions) - Modify guild channel positions
- [`listActiveGuildThreads`](./api/guilds.md#listActiveGuildThreads) - List active guild threads
- [`getGuildMember`](./api/guilds.md#getGuildMember) - Get guild member
- [`listGuildMembers`](./api/guilds.md#listGuildMembers) - List guild members
- [`searchGuildMembers`](./api/guilds.md#searchGuildMembers) - Search guild members
- [`addGuildMember`](./api/guilds.md#addGuildMember) - Add guild member
- [`modifyGuildMember`](./api/guilds.md#modifyGuildMember) - Modify guild member
- [`modifyCurrentMember`](./api/general.md#modifyCurrentMember) - Modify current member
- [`addGuildMemberRole`](./api/guilds.md#addGuildMemberRole) - Add guild member role
- [`removeGuildMemberRole`](./api/guilds.md#removeGuildMemberRole) - Remove guild member role
- [`removeGuildMember`](./api/guilds.md#removeGuildMember) - Remove guild member
- [`getGuildBans`](./api/guilds.md#getGuildBans) - Get guild bans
- [`getGuildBan`](./api/guilds.md#getGuildBan) - Get guild ban
- [`createGuildBan`](./api/guilds.md#createGuildBan) - Create guild ban
- [`removeGuildBan`](./api/guilds.md#removeGuildBan) - Remove guild ban
- [`getGuildRoles`](./api/guilds.md#getGuildRoles) - Get guild roles
- [`createGuildRole`](./api/guilds.md#createGuildRole) - Create guild role
- [`modifyGuildRolePositions`](./api/guilds.md#modifyGuildRolePositions) - Modify guild role positions
- [`modifyGuildRole`](./api/guilds.md#modifyGuildRole) - Modify guild role
- [`modifyGuildMfaLevel`](./api/guilds.md#modifyGuildMfaLevel) - Modify guild mfa level
- [`deleteGuildRole`](./api/guilds.md#deleteGuildRole) - Delete guild role
- [`getGuildPruneCount`](./api/guilds.md#getGuildPruneCount) - Get guild prune count
- [`beginGuildPrune`](./api/guilds.md#beginGuildPrune) - Begin guild prune
- [`getGuildVoiceRegions`](./api/guilds.md#getGuildVoiceRegions) - Get guild voice regions
- [`getGuildInvites`](./api/guilds.md#getGuildInvites) - Get guild invites
- [`getGuildIntegrations`](./api/guilds.md#getGuildIntegrations) - Get guild integrations
- [`deleteGuildIntegrations`](./api/guilds.md#deleteGuildIntegrations) - Delete guild integrations
- [`getGuildWidgetSettings`](./api/guilds.md#getGuildWidgetSettings) - Get guild widget settings
- [`modifyGuildWidget`](./api/guilds.md#modifyGuildWidget) - Modify guild widget
- [`getGuildWidget`](./api/guilds.md#getGuildWidget) - Get guild widget
- [`getGuildVanityUrl`](./api/guilds.md#getGuildVanityUrl) - Get guild vanity url
- [`getGuildWidgetImage`](./api/guilds.md#getGuildWidgetImage) - Get guild widget image
- [`getGuildWelcomeScreen`](./api/guilds.md#getGuildWelcomeScreen) - Get guild welcome screen
- [`modifyGuildWelcomeScreen`](./api/guilds.md#modifyGuildWelcomeScreen) - Modify guild welcome screen
- [`getGuildOnboarding`](./api/guilds.md#getGuildOnboarding) - Get guild onboarding
- [`modifyGuildOnboarding`](./api/guilds.md#modifyGuildOnboarding) - Modify guild onboarding
- [`modifyCurrentUserVoiceState`](./api/users.md#modifyCurrentUserVoiceState) - Modify current user voice state
- [`modifyUserVoiceState`](./api/users.md#modifyUserVoiceState) - Modify user voice state
- [`listScheduledEventsForGuild`](./api/guilds.md#listScheduledEventsForGuild) - List scheduled events for guild
- [`createGuildScheduledEvent`](./api/guilds.md#createGuildScheduledEvent) - Create guild scheduled event
- [`getGuildScheduledEvent`](./api/guilds.md#getGuildScheduledEvent) - Get guild scheduled event
- [`modifyGuildScheduledEvent`](./api/guilds.md#modifyGuildScheduledEvent) - Modify guild scheduled event
- [`deleteGuildScheduledEvent`](./api/guilds.md#deleteGuildScheduledEvent) - Delete guild scheduled event
- [`getGuildScheduledEventUsers`](./api/guilds.md#getGuildScheduledEventUsers) - Get guild scheduled event users
- [`getGuildTemplate`](./api/guilds.md#getGuildTemplate) - Get guild template
- [`createGuildFromTemplate`](./api/guilds.md#createGuildFromTemplate) - Create guild from template
- [`getGuildTemplates`](./api/guilds.md#getGuildTemplates) - Get guild templates
- [`createGuildTemplate`](./api/guilds.md#createGuildTemplate) - Create guild template
- [`syncGuildTemplate`](./api/guilds.md#syncGuildTemplate) - Sync guild template
- [`modifyGuildTemplate`](./api/guilds.md#modifyGuildTemplate) - Modify guild template
- [`deleteGuildTemplate`](./api/guilds.md#deleteGuildTemplate) - Delete guild template
- [`getInvite`](./api/general.md#getInvite) - Get invite
- [`deleteInvite`](./api/general.md#deleteInvite) - Delete invite
- [`createStageInstance`](./api/general.md#createStageInstance) - Create stage instance
- [`getStageInstance`](./api/general.md#getStageInstance) - Get stage instance
- [`modifyStageInstance`](./api/general.md#modifyStageInstance) - Modify stage instance
- [`deleteStageInstance`](./api/general.md#deleteStageInstance) - Delete stage instance
- [`getSticker`](./api/general.md#getSticker) - Get sticker
- [`listNitroStickerPacks`](./api/general.md#listNitroStickerPacks) - List nitro sticker packs
- [`listGuildStickers`](./api/guilds.md#listGuildStickers) - List guild stickers
- [`getGuildStickers`](./api/guilds.md#getGuildStickers) - Get guild stickers
- [`getGuildSticker`](./api/guilds.md#getGuildSticker) - Get guild sticker
- [`createGuildSticker`](./api/guilds.md#createGuildSticker) - Create guild sticker
- [`modifyGuildSticker`](./api/guilds.md#modifyGuildSticker) - Modify guild sticker
- [`deleteGuildSticker`](./api/guilds.md#deleteGuildSticker) - Delete guild sticker
- [`getCurrentUser`](./api/users.md#getCurrentUser) - Get current user
- [`getUser`](./api/users.md#getUser) - Get user
- [`modifyCurrentUser`](./api/users.md#modifyCurrentUser) - Modify current user
- [`getCurrentUserGuilds`](./api/guilds.md#getCurrentUserGuilds) - Get current user guilds
- [`getCurrentUserGuildMember`](./api/guilds.md#getCurrentUserGuildMember) - Get current user guild member
- [`leaveGuild`](./api/guilds.md#leaveGuild) - Leave guild
- [`createDm`](./api/general.md#createDm) - Create dm
- [`createGroupDm`](./api/general.md#createGroupDm) - Create group dm
- [`getUserConnections`](./api/users.md#getUserConnections) - Get user connections
- [`getUserApplicationRoleConnections`](./api/users.md#getUserApplicationRoleConnections) - Get user application role connections
- [`updateUserApplicationRoleConnections`](./api/users.md#updateUserApplicationRoleConnections) - Update user application role connections
- [`listVoiceRegions`](./api/general.md#listVoiceRegions) - List voice regions
- [`createWebhook`](./api/webhooks.md#createWebhook) - Create webhook
- [`getChannelWebhooks`](./api/channels.md#getChannelWebhooks) - Get channel webhooks
- [`getGuildWebhooks`](./api/guilds.md#getGuildWebhooks) - Get guild webhooks
- [`getWebhook`](./api/webhooks.md#getWebhook) - Get webhook
- [`getWebhookWithToken`](./api/webhooks.md#getWebhookWithToken) - Get webhook with token
- [`modifyWebhook`](./api/webhooks.md#modifyWebhook) - Modify webhook
- [`modifyWebhookWIthToken`](./api/webhooks.md#modifyWebhookWIthToken) - Modify webhook w ith token
- [`deleteWebhook`](./api/webhooks.md#deleteWebhook) - Delete webhook
- [`deleteWebhookWithToken`](./api/webhooks.md#deleteWebhookWithToken) - Delete webhook with token
- [`executeWebhook`](./api/webhooks.md#executeWebhook) - Execute webhook
- [`executeSlackCompatibleWebhook`](./api/webhooks.md#executeSlackCompatibleWebhook) - Execute slack compatible webhook
- [`executeGitHubCompatibleWebhook`](./api/webhooks.md#executeGitHubCompatibleWebhook) - Execute git hub compatible webhook
- [`getWebhookMessage`](./api/channels.md#getWebhookMessage) - Get webhook message
- [`editWebhookMessage`](./api/channels.md#editWebhookMessage) - Edit webhook message
- [`deleteWebhookMessage`](./api/channels.md#deleteWebhookMessage) - Delete webhook message
