<?php

namespace Kyzegs\Laracord\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array getGlobalApplicationCommands(string $applicationId, array $query = [])
 * @method static array createGlobalApplicationCommand(string $applicationId, array $data)
 * @method static array getGlobalApplicationCommand(string $applicationId, string $commandId)
 * @method static array editGlobalApplicationCommand(string $applicationId, string $commandId, array $data)
 * @method static array deleteGlobalApplicationCommand(string $applicationId, string $commandId)
 * @method static array bulkOverwriteGlobalApplicationCommands(string $applicationId, array $data)
 * @method static array getGuildApplicationCommands(string $applicationId, string $guildId, array $query = [])
 * @method static array createGuildApplicationCommand(string $applicationId, string $guildId, array $data)
 * @method static array getGuildApplicationCommand(string $applicationId, string $guildId, string $commandId)
 * @method static array editGuildApplicationCommand(string $applicationId, string $guildId, string $commandId, array $data)
 * @method static array deleteGuildApplicationCommand(string $applicationId, string $guildId, string $commandId)
 * @method static array bulkOverwriteGuildApplicationCommands(string $applicationId, string $guildId, array $data)
 * @method static array getGuildApplicationCommandPermissions(string $applicationId, string $guildId)
 * @method static array getApplicationCommandPermissions(string $applicationId, string $guildId, string $commandId)
 * @method static array editAPplicationCommandPermissions(string $applicationId, string $guildId, string $commandId, array $data)
 * @method static array batchEditApplicationCommandPermissions(string $applicationId, string $guildId, array $data)
 * @method static array getCurrentApplication()
 * @method static array getApplicationRoleConnectionMetadataRecords(string $applicationId)
 * @method static array updateApplicationRoleConnectionMetadataRecords(string $applicationId, array $data)
 * @method static array getGuildAuditLog(string $guildId, array $query = [])
 * @method static array listAutoModerationRulesForGuild(string $guildId)
 * @method static array getAutoModerationRule(string $guildId, string $ruleId)
 * @method static array createAutoModerationRule(string $guildId, array $data)
 * @method static array modifyAutoModerationRule(string $guildId, string $ruleId, array $data)
 * @method static array deleteAutoModerationRule(string $guildId, string $ruleId)
 * @method static array getChannel(int $channelId)
 * @method static array modifyChannel(int $channelId, array $data)
 * @method static array deleteChannel(int $channelId)
 * @method static array getChannelMessages(int $channelId, array $data = [])
 * @method static array getChannelMessage(int $channelId, int $messageId)
 * @method static array createMessage(int $channelId, array $data)
 * @method static array crosspostMessage(int $channelId, int $messageId)
 * @method static array createReaction(int $channelId, int $messageId, string $emoji)
 * @method static array deleteOwnReaction(int $channelId, int $messageId, string $emoji)
 * @method static array deleteUserReaction(int $channelId, int $messageId, string $emoji, int $userId)
 * @method static array getReactions(int $channelId, int $messageId, string $emoji)
 * @method static array deleteAllReactions(int $channelId, int $messageId)
 * @method static array deleteAllReactionsForEmoji(int $channelId, int $messageId, string $emoji)
 * @method static array editMessage(int $channelId, int $messageId, array $data)
 * @method static array deleteMessage(int $channelId, int $messageId)
 * @method static array bulkDeleteMessages(int $channelId, array $data)
 * @method static array editChannelPermissions(int $channelId, int $overwriteId, array $data)
 * @method static array getChannelInvites(int $channelId)
 * @method static array createChannelInvite(int $channelId, array $data)
 * @method static array deleteChannelPermission(int $channelId, int $overwriteId)
 * @method static array followAnnouncementChannel(int $channelId, array $data)
 * @method static array triggerTypingIndicator(int $channelId)
 * @method static array getPinnedMessages(int $channelId)
 * @method static array pinMessage(int $channelId, int $messageId)
 * @method static array unpinMessage(int $channelId, int $messageId)
 * @method static array startThreadFromMessage(int $channelId, int $messageId, array $data)
 * @method static array startThreadWithoutMessage(int $channelId, array $data)
 * @method static array startThreadInForumChannel(int $channelId, array $data)
 * @method static array joinThread(int $channelId)
 * @method static array addThreadMember(int $channelId, int $userId)
 * @method static array leaveThread(int $channelId)
 * @method static array removeThreadMember(int $channelId, int $userId)
 * @method static array getThreadMember(int $channelId, int $userId)
 * @method static array listThreadMembers(int $channelId)
 * @method static array listPublicArchivedThreads(int $channelId, array $data = [])
 * @method static array listPrivateArchivedThreads(int $channelId, array $data = [])
 * @method static array listJoinedPrivateArchivedThreads(int $channelId, array $data = [])
 * @method static array listGuildEmojis(int $guildId)
 * @method static array getGuildEmoji(int $guildId, int $emojiId)
 * @method static array createGuildEmoji(int $guildId, array $data)
 * @method static array modifyGuildEmoji(int $guildId, int $emojiId, array $data)
 * @method static array deleteGuildEmoji(int $guildId, int $emojiId)
 * @method static array createGuild(array $data)
 * @method static array getGuild(int $guildId)
 * @method static array getGuildPreview(int $guildId)
 * @method static array modifyGuild(int $guildId, array $data)
 * @method static array deleteGuild(int $guildId)
 * @method static array getGuildChannels(int $guildId)
 * @method static array createGuildChannel(int $guildId, array $data)
 * @method static array modifyGuildChannelPositions(int $guildId, array $data)
 * @method static array listActiveGuildThreads(int $guildId)
 * @method static array getGuildMember(int $guildId, int $userId)
 * @method static array listGuildMembers(int $guildId, array $data = [])
 * @method static array searchGuildMembers(int $guildId, array $data = [])
 * @method static array addGuildMember(int $guildId, int $userId, array $data)
 * @method static array modifyGuildMember(int $guildId, int $userId, array $data)
 * @method static array modifyCurrentMember(int $guildId, array $data)
 * @method static array addGuildMemberRole(int $guildId, int $userId, int $roleId)
 * @method static array removeGuildMemberRole(int $guildId, int $userId, int $roleId)
 * @method static array removeGuildMember(int $guildId, int $userId)
 * @method static array getGuildBans(int $guildId)
 * @method static array getGuildBan(int $guildId, int $userId)
 * @method static array createGuildBan(int $guildId, int $userId, array $data = [])
 * @method static array removeGuildBan(int $guildId, int $userId)
 * @method static array getGuildRoles(int $guildId)
 * @method static array createGuildRole(int $guildId, array $data)
 * @method static array modifyGuildRolePositions(int $guildId, array $data)
 * @method static array modifyGuildRole(int $guildId, int $roleId, array $data)
 * @method static array modifyGuildMfaLevel(int $guildId, array $data)
 * @method static array deleteGuildRole(int $guildId, int $roleId)
 * @method static array getGuildPruneCount(int $guildId)
 * @method static array beginGuildPrune(int $guildId)
 * @method static array getGuildVoiceRegions(int $guildId)
 * @method static array getGuildInvites(int $guildId)
 * @method static array getGuildIntegrations(int $guildId)
 * @method static array deleteGuildIntegrations(int $guildId, int $integrationId)
 * @method static array getGuildWidgetSettings(int $guildId)
 * @method static array modifyGuildWidget(int $guildId, array $data)
 * @method static array getGuildWidget(int $guildId)
 * @method static array getGuildVanityUrl(int $guildId)
 * @method static array getGuildWidgetImage(int $guildId, array $data = [])
 * @method static array getGuildWelcomeScreen(int $guildId)
 * @method static array modifyGuildWelcomeScreen(int $guildId, array $data)
 * @method static array getGuildOnboarding(int $guildId)
 * @method static array modifyGuildOnboarding(int $guildId, array $data)
 * @method static array modifyCurrentUserVoiceState(int $guildId, array $data)
 * @method static array modifyUserVoiceState(int $guildId, int $userId, array $data)
 * @method static array listScheduledEventsForGuild(int $guildId)
 * @method static array createGuildScheduledEvent(int $guildId, array $data)
 * @method static array getGuildScheduledEvent(int $guildId, string $guildScheduledEventId)
 * @method static array modifyGuildScheduledEvent(int $guildId, string $guildScheduledEventId, array $data)
 * @method static array deleteGuildScheduledEvent(int $guildId, string $guildScheduledEventId)
 * @method static array getGuildScheduledEventUsers(int $guildId, string $guildScheduledEventId)
 * @method static array getGuildTemplate(string $templateCode)
 * @method static array createGuildFromTemplate(string $templateCode, array $data)
 * @method static array getGuildTemplates(int $guildId)
 * @method static array createGuildTemplate(int $guildId, array $data)
 * @method static array syncGuildTemplate(int $guildId, string $templateCode)
 * @method static array modifyGuildTemplate(int $guildId, string $templateCode, array $data)
 * @method static array deleteGuildTemplate(int $guildId, string $templateCode)
 * @method static array getInvite(string $inviteCode, array $data = [])
 * @method static array deleteInvite(string $inviteCode)
 * @method static array createStageInstance(int $channelId, array $data)
 * @method static array getStageInstance(int $channelId)
 * @method static array modifyStageInstance(int $channelId, array $data)
 * @method static array deleteStageInstance(int $channelId)
 * @method static array getSticker(int $stickerId)
 * @method static array listNitroStickerPacks()
 * @method static array listGuildStickers(int $guildId)
 * @method static array getGuildStickers(int $guildId, int $stickerId)
 * @method static array getGuildSticker(int $guildId, int $stickerId)
 * @method static array createGuildSticker(int $guildId, array $data)
 * @method static array modifyGuildSticker(int $guildId, int $stickerId, array $data)
 * @method static array deleteGuildSticker(int $guildId, int $stickerId)
 * @method static array getCurrentUser()
 * @method static array getUser(int $userId)
 * @method static array modifyCurrentUser(array $data)
 * @method static array getCurrentUserGuilds(array $data = [])
 * @method static array getCurrentUserGuildMember(int $guildId)
 * @method static array leaveGuild(int $guildId)
 * @method static array createDm(array $data)
 * @method static array createGroupDm(array $data)
 * @method static array getUserConnections()
 * @method static array getUserApplicationRoleConnections(int $guildId)
 * @method static array updateUserApplicationRoleConnections(int $guildId, array $data)
 * @method static array listVoiceRegions()
 * @method static array createWebhook(int $channelId, array $data)
 * @method static array getChannelWebhooks(int $channelId)
 * @method static array getGuildWebhooks(int $guildId)
 * @method static array getWebhook(int $webhookId)
 * @method static array getWebhookWithToken(int $webhookId, string $webhookToken)
 * @method static array modifyWebhook(int $webhookId, array $data)
 * @method static array modifyWebhookWIthToken(int $webhookId, string $webhookToken, array $data)
 * @method static array deleteWebhook(int $webhookId)
 * @method static array deleteWebhookWithToken(int $webhookId, string $webhookToken)
 * @method static array executeWebhook(int $webhookId, string $webhookToken, array $data)
 * @method static array executeSlackCompatibleWebhook(int $webhookId, string $webhookToken, array $data)
 * @method static array executeGitHubCompatibleWebhook(int $webhookId, string $webhookToken, array $data)
 * @method static array getWebhookMessage(int $webhookId, string $webhookToken, string $messageId)
 * @method static array editWebhookMessage(int $webhookId, string $webhookToken, string $messageId, array $data)
 * @method static array deleteWebhookMessage(int $webhookId, string $webhookToken, string $messageId)
 *
 * @see \Kyzegs\Laracord\Client
 */
class Laracord extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'laracord';
    }
}
