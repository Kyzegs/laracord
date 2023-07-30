<?php

namespace Kyzegs\Laracord;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Sleep;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Client
{
    public function __construct(private GuzzleClient $client)
    {
    }

    private function request(Route $route, array $payload = null, array $query = []): array
    {
        for ($tries = 0; $tries < 3; $tries++) {
            $bucketHash = $route->getBucketHash()->get();
            $ratelimit = $route->getBucket()->get();

            $lock = $route->getBucket()->lock();

            try {
                $lock->block(PHP_INT_MAX);

                // TODO: Also check for a expiration. If it's after the expires at then just do the request
                if ($ratelimit->getRemaining() === 0) {
                    Log::debug(sprintf('Sleeping rate limit bucket %s for %.2f seconds.', $bucketHash ?? $route->getKey(), $ratelimit->getResetAfter()));

                    // TODO: Calculate the reset after on demand? That way it doesn't intervene sleep the same amount of time even if 4s have passed. We can even use sleep until...
                    Sleep::sleep($ratelimit->getResetAfter());
                }

                // NOTE: Handle these exceptions by retrying...?
                $response = $this->client->request($route->getMethod(), $route->getUrl(), [
                    RequestOptions::HTTP_ERRORS => false,
                    RequestOptions::JSON => $payload,
                    RequestOptions::QUERY => $query,
                ]);

                $discordHash = Arr::get($response->getHeader('X-Ratelimit-Bucket'), 0);

                $body = $response->getBody();
                $contents = $body->getContents();
                $statusCode = $response->getStatusCode();

                $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

                if ($discordHash !== null && $bucketHash !== $discordHash) {
                    if ($bucketHash !== null) {
                        Log::debug(sprintf('A route (%s) has changed hashes: %s -> %s.', $route->getKey(), $bucketHash, $discordHash));

                        $route->getBucket()->forget();
                    } elseif ($route->getBucketHash()->missing()) {
                        Log::debug(sprintf('%s has found its initial rate limit bucket hash (%s).', $route->getKey(), $discordHash));
                    }

                    $route->getBucketHash()->put($discordHash);
                    $route->getBucket()->put($ratelimit);
                }

                if ($response->hasHeader('X-Ratelimit-Remaining') && $response->getStatusCode() !== 429) {
                    $route->getBucket()->put($ratelimit->update($response));

                    if ($ratelimit->getRemaining() === 0) {
                        Log::debug(sprintf('A rate limit bucket (%s) has been exhausted. Pre-emptively rate limiting...', $discordHash ?? $route->getKey()));
                    }
                }

                if ($response->getStatusCode() >= 200 && $response->getStatusCode() <= 300) {
                    Log::debug(sprintf('%s %s has received %s', $route->getMethod(), $route->getUrl(), json_encode($data)));

                    return $data;
                }

                if ($response->getStatusCode() === 429) {
                    // NOTE: Should we update the rate limit here too?

                    if (! $response->hasHeader('Via')) {
                        throw new HttpException($statusCode, $contents);
                    }

                    if ($ratelimit->getRemaining() > 0) {
                        Log::debug(sprintf('%s %s received a 429 despite having %d remaining requests. This is a sub-ratelimit.', $route->getMethod(), $route->getUrl(), $ratelimit->getRemaining()));
                    }

                    Log::warning(sprintf('We are being rate limited. %s %s responded with 429. Retrying in %.2f seconds.', $route->getMethod(), $route->getUrl(), $data['retry_after']));
                    Log::debug(sprintf('Rate limit is being handled by bucket hash %s with %s major parameters.', $bucketHash, $route->getMajorParameters()));

                    continue;
                }

                if (in_array($response->getStatusCode(), [500, 502, 504, 524], true)) {
                    $backoff = 1 + $tries * 2;

                    Log::debug(sprintf('Encountered a %s status code. Retrying in %s seconds.', $statusCode, $backoff));
                    Sleep::sleep($backoff);

                    continue;
                }

                // TODO: Add global check and throw errors for other methods

                throw new HttpException($statusCode, $contents);
            } finally {
                $lock->release();
            }
        }

        //if response is not None:
        //        # We've run out of retries, raise.
        //        if response.status >= 500:
        //            raise DiscordServerError(response, data)
        //
        //        raise HTTPException(response, data)
        //
        //    raise RuntimeError('Unreachable code in HTTP handling')
    }

    public function getGlobalApplicationCommands(string $applicationId, array $query = []): array
    {
        return $this->request(new Route('GET', '/applications/{application_id}/commands', ['application_id' => $applicationId]), query: $query);
    }

    public function createGlobalApplicationCommand(string $applicationId, array $data): array
    {
        return $this->request(new Route('POST', '/applications/{application_id}/commands', ['application_id' => $applicationId]), $data);
    }

    public function getGlobalApplicationCommand(string $applicationId, string $commandId): array
    {
        return $this->request(new Route('GET', '/applications/{application_id}/commands/{command_id}', ['application_id' => $applicationId, 'command_id' => $commandId]));
    }

    public function editGlobalApplicationCommand(string $applicationId, string $commandId, array $data): array
    {
        return $this->request(new Route('PATCH', '/applications/{application_id}/commands/{command_id}', ['application_id' => $applicationId, 'command_id' => $commandId]), $data);
    }

    public function deleteGlobalApplicationCommand(string $applicationId, string $commandId): array
    {
        return $this->request(new Route('DELETE', '/applications/{application_id}/commands/{command_id}', ['application_id' => $applicationId, 'command_id' => $commandId]));
    }

    public function bulkOverwriteGlobalApplicationCommands(string $applicationId, array $data): array
    {
        return $this->request(new Route('PUT', '/applications/{application_id}/commands', ['application_id' => $applicationId]), $data);
    }

    public function getGuildApplicationCommands(string $applicationId, string $guildId, array $query = []): array
    {
        return $this->request(new Route('GET', '/applications/{application_id}/guilds/{guild_id}/commands', ['application_id' => $applicationId, 'guild_id' => $guildId]), query: $query);
    }

    public function createGuildApplicationCommand(string $applicationId, string $guildId, array $data): array
    {
        return $this->request(new Route('POST', '/applications/{application_id}/guilds/{guild_id}/commands', ['application_id' => $applicationId, 'guild_id' => $guildId]), $data);
    }

    public function getGuildApplicationCommand(string $applicationId, string $guildId, string $commandId): array
    {
        return $this->request(new Route('GET', '/applications/{application_id}/guilds/{guild_id}/commands/{command_id}', ['application_id' => $applicationId, 'guild_id' => $guildId, 'command_id' => $commandId]));
    }

    public function editGuildApplicationCommand(string $applicationId, string $guildId, string $commandId, array $data): array
    {
        return $this->request(new Route('PATCH', '/applications/{application_id}/guilds/{guild_id}/commands/{command_id}', ['application_id' => $applicationId, 'guild_id' => $guildId, 'command_id' => $commandId]), $data);
    }

    public function deleteGuildApplicationCommand(string $applicationId, string $guildId, string $commandId): array
    {
        return $this->request(new Route('DELETE', '/applications/{application_id}/guilds/{guild_id}/commands/{command_id}', ['application_id' => $applicationId, 'guild_id' => $guildId, 'command_id' => $commandId]));
    }

    public function bulkOverwriteGuildApplicationCommands(string $applicationId, string $guildId, array $data): array
    {
        return $this->request(new Route('PUT', '/applications/{application_id}/guilds/{guild_id}/commands', ['application_id' => $applicationId, 'guild_id' => $guildId]), $data);
    }

    public function getGuildApplicationCommandPermissions(string $applicationId, string $guildId): array
    {
        return $this->request(new Route('GET', '/applications/{application_id}/guilds/{guild_id}/commands/permissions', ['application_id' => $applicationId, 'guild_id' => $guildId]));
    }

    public function getApplicationCommandPermissions(string $applicationId, string $guildId, string $commandId): array
    {
        return $this->request(new Route('GET', '/applications/{application_id}/guilds/{guild_id}/commands/{command_id}/permissions', ['application_id' => $applicationId, 'guild_id' => $guildId, 'command_id' => $commandId]));
    }

    public function editAPplicationCommandPermissions(string $applicationId, string $guildId, string $commandId, array $data): array
    {
        return $this->request(new Route('PUT', '/applications/{application_id}/guilds/{guild_id}/commands/{command_id}/permissions', ['application_id' => $applicationId, 'guild_id' => $guildId, 'command_id' => $commandId]), $data);
    }

    public function batchEditApplicationCommandPermissions(string $applicationId, string $guildId, array $data): array
    {
        return $this->request(new Route('PUT', '/applications/{application_id}/guilds/{guild_id}/commands/permissions', ['application_id' => $applicationId, 'guild_id' => $guildId]), $data);
    }

    public function getCurrentApplication(): array
    {
        return $this->request(new Route('GET', '/applications/@me'));
    }

    public function getApplicationRoleConnectionMetadataRecords(string $applicationId): array
    {
        return $this->request(new Route('GET', '/applications/{application_id}/role-connections/metadata', ['application_id' => $applicationId]));
    }

    public function updateApplicationRoleConnectionMetadataRecords(string $applicationId, array $data): array
    {
        return $this->request(new Route('PUT', '/applications/{application_id}/role-connections/metadata', ['application_id' => $applicationId]), $data);
    }

    public function getGuildAuditLog(string $guildId, array $query = []): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/audit-logs', ['guild_id' => $guildId]), query: $query);
    }

    public function listAutoModerationRulesForGuild(string $guildId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/auto-moderation/rules', ['guild_id' => $guildId]));
    }

    public function getAutoModerationRule(string $guildId, string $ruleId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/auto-moderation/rules/{auto_moderation_rule_id}', ['guild_id' => $guildId, 'auto_moderation_rule_id' => $ruleId]));
    }

    public function createAutoModerationRule(string $guildId, array $data): array
    {
        return $this->request(new Route('POST', '/guilds/{guild_id}/auto-moderation/rules', ['guild_id' => $guildId]), $data);
    }

    public function modifyAutoModerationRule(string $guildId, string $ruleId, array $data): array
    {
        return $this->request(new Route('PATCH', '/guilds/{guild_id}/auto-moderation/rules/{auto_moderation_rule_id}', ['guild_id' => $guildId, 'auto_moderation_rule_id' => $ruleId]), $data);
    }

    public function deleteAutoModerationRule(string $guildId, string $ruleId): array
    {
        return $this->request(new Route('DELETE', '/guilds/{guild_id}/auto-moderation/rules/{auto_moderation_rule_id}', ['guild_id' => $guildId, 'auto_moderation_rule_id' => $ruleId]));
    }

    public function getChannel(int $channelId): array
    {
        return $this->request(new Route('GET', '/channels/{channel_id}', ['channel_id' => $channelId]));
    }

    public function modifyChannel(int $channelId, array $data): array
    {
        return $this->request(new Route('PATCH', '/channels/{channel_id}', ['channel_id' => $channelId]), $data);
    }

    public function deleteChannel(int $channelId): array
    {
        return $this->request(new Route('DELETE', '/channels/{channel_id}', ['channel_id' => $channelId]));
    }

    public function getChannelMessages(int $channelId, array $data = []): array
    {
        return $this->request(new Route('GET', '/channels/{channel_id}/messages', ['channel_id' => $channelId]));
    }

    public function getChannelMessage(int $channelId, int $messageId): array
    {
        return $this->request(new Route('GET', '/channels/{channel_id}/messages/{message_id}', ['channel_id' => $channelId, 'message_id' => $messageId]));
    }

    public function createMessage(int $channelId, array $data): array
    {
        return $this->request(new Route('POST', '/channels/{channel_id}/messages', ['channel_id' => $channelId]), $data);
    }

    public function crosspostMessage(int $channelId, int $messageId): array
    {
        return $this->request(new Route('POST', '/channels/{channel_id}/messages/{message_id}/crosspost', ['channel_id' => $channelId, 'message_id' => $messageId]));
    }

    public function createReaction(int $channelId, int $messageId, string $emoji): array
    {
        return $this->request(new Route('PUT', '/channels/{channel_id}/messages/{message_id}/reactions/{emoji}/@me', ['channel_id' => $channelId, 'message_id' => $messageId, 'emoji' => $emoji]));
    }

    public function deleteOwnReaction(int $channelId, int $messageId, string $emoji): array
    {
        return $this->request(new Route('DELETE', '/channels/{channel_id}/messages/{message_id}/reactions/{emoji}/@me', ['channel_id' => $channelId, 'message_id' => $messageId, 'emoji' => $emoji]));
    }

    public function deleteUserReaction(int $channelId, int $messageId, string $emoji, int $userId): array
    {
        return $this->request(new Route('DELETE', '/channels/{channel_id}/messages/{message_id}/reactions/{emoji}/{user_id}', ['channel_id' => $channelId, 'message_id' => $messageId, 'emoji' => $emoji, 'user_id' => $userId]));
    }

    public function getReactions(int $channelId, int $messageId, string $emoji): array
    {
        return $this->request(new Route('GET', '/channels/{channel_id}/messages/{message_id}/reactions/{emoji}', ['channel_id' => $channelId, 'message_id' => $messageId, 'emoji' => $emoji]));
    }

    public function deleteAllReactions(int $channelId, int $messageId): array
    {
        return $this->request(new Route('DELETE', '/channels/{channel_id}/messages/{message_id}/reactions', ['channel_id' => $channelId, 'message_id' => $messageId]));
    }

    public function deleteAllReactionsForEmoji(int $channelId, int $messageId, string $emoji): array
    {
        return $this->request(new Route('DELETE', '/channels/{channel_id}/messages/{message_id}/reactions/{emoji}', ['channel_id' => $channelId, 'message_id' => $messageId, 'emoji' => $emoji]));
    }

    public function editMessage(int $channelId, int $messageId, array $data): array
    {
        return $this->request(new Route('PATCH', '/channels/{channel_id}/messages/{message_id}', ['channel_id' => $channelId, 'message_id' => $messageId]), $data);
    }

    public function deleteMessage(int $channelId, int $messageId): array
    {
        return $this->request(new Route('DELETE', '/channels/{channel_id}/messages/{message_id}', ['channel_id' => $channelId, 'message_id' => $messageId]));
    }

    public function bulkDeleteMessages(int $channelId, array $data): array
    {
        return $this->request(new Route('POST', '/channels/{channel_id}/messages/bulk-delete', ['channel_id' => $channelId]), $data);
    }

    public function editChannelPermissions(int $channelId, int $overwriteId, array $data): array
    {
        return $this->request(new Route('PUT', '/channels/{channel_id}/permissions/{overwrite_id}', ['channel_id' => $channelId, 'overwrite_id' => $overwriteId]), $data);
    }

    public function getChannelInvites(int $channelId): array
    {
        return $this->request(new Route('GET', '/channels/{channel_id}/invites', ['channel_id' => $channelId]));
    }

    public function createChannelInvite(int $channelId, array $data): array
    {
        return $this->request(new Route('POST', '/channels/{channel_id}/invites', ['channel_id' => $channelId]), $data);
    }

    public function deleteChannelPermission(int $channelId, int $overwriteId): array
    {
        return $this->request(new Route('DELETE', '/channels/{channel_id}/permissions/{overwrite_id}', ['channel_id' => $channelId, 'overwrite_id' => $overwriteId]));
    }

    public function followAnnouncementChannel(int $channelId, array $data): array
    {
        return $this->request(new Route('POST', '/channels/{channel_id}/followers', ['channel_id' => $channelId]), $data);
    }

    public function triggerTypingIndicator(int $channelId): array
    {
        return $this->request(new Route('POST', '/channels/{channel_id}/typing', ['channel_id' => $channelId]));
    }

    public function getPinnedMessages(int $channelId): array
    {
        return $this->request(new Route('GET', '/channels/{channel_id}/pins', ['channel_id' => $channelId]));
    }

    public function pinMessage(int $channelId, int $messageId): array
    {
        return $this->request(new Route('PUT', '/channels/{channel_id}/pins/{message_id}', ['channel_id' => $channelId, 'message_id' => $messageId]));
    }

    public function unpinMessage(int $channelId, int $messageId): array
    {
        return $this->request(new Route('DELETE', '/channels/{channel_id}/pins/{message_id}', ['channel_id' => $channelId, 'message_id' => $messageId]));
    }

    public function startThreadFromMessage(int $channelId, int $messageId, array $data): array
    {
        return $this->request(new Route('POST', '/channels/{channel_id}/messages/{message_id}/threads', ['channel_id' => $channelId, 'message_id' => $messageId]), $data);
    }

    public function startThreadWithoutMessage(int $channelId, array $data): array
    {
        return $this->request(new Route('POST', '/channels/{channel_id}/threads', ['channel_id' => $channelId]), $data);
    }

    public function startThreadInForumChannel(int $channelId, array $data): array
    {
        return $this->request(new Route('POST', '/channels/{channel_id}/threads', ['channel_id' => $channelId]), $data);
    }

    public function joinThread(int $channelId): array
    {
        return $this->request(new Route('PUT', '/channels/{channel_id}/thread-members/@me', ['channel_id' => $channelId]));
    }

    public function addThreadMember(int $channelId, int $userId): array
    {
        return $this->request(new Route('PUT', '/channels/{channel_id}/thread-members/{user_id}', ['channel_id' => $channelId, 'user_id' => $userId]));
    }

    public function leaveThread(int $channelId): array
    {
        return $this->request(new Route('DELETE', '/channels/{channel_id}/thread-members/@me', ['channel_id' => $channelId]));
    }

    public function removeThreadMember(int $channelId, int $userId): array
    {
        return $this->request(new Route('DELETE', '/channels/{channel_id}/thread-members/{user_id}', ['channel_id' => $channelId, 'user_id' => $userId]));
    }

    public function getThreadMember(int $channelId, int $userId): array
    {
        return $this->request(new Route('GET', '/channels/{channel_id}/thread-members/{user_id}', ['channel_id' => $channelId, 'user_id' => $userId]));
    }

    public function listThreadMembers(int $channelId): array
    {
        return $this->request(new Route('GET', '/channels/{channel_id}/thread-members', ['channel_id' => $channelId]));
    }

    public function listPublicArchivedThreads(int $channelId, array $data = []): array
    {
        return $this->request(new Route('GET', '/channels/{channel_id}/threads/archived/public', ['channel_id' => $channelId]), $data);
    }

    public function listPrivateArchivedThreads(int $channelId, array $data = []): array
    {
        return $this->request(new Route('GET', '/channels/{channel_id}/threads/archived/private', ['channel_id' => $channelId]), $data);
    }

    public function listJoinedPrivateArchivedThreads(int $channelId, array $data = []): array
    {
        return $this->request(new Route('GET', '/channels/{channel_id}/users/@me/threads/archived/private', ['channel_id' => $channelId]), $data);
    }

    public function listGuildEmojis(int $guildId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/emojis', ['guild_id' => $guildId]));
    }

    public function getGuildEmoji(int $guildId, int $emojiId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/emojis/{emoji_id}', ['guild_id' => $guildId, 'emoji_id' => $emojiId]));
    }

    public function createGuildEmoji(int $guildId, array $data): array
    {
        return $this->request(new Route('POST', '/guilds/{guild_id}/emojis', ['guild_id' => $guildId]), $data);
    }

    public function modifyGuildEmoji(int $guildId, int $emojiId, array $data): array
    {
        return $this->request(new Route('PATCH', '/guilds/{guild_id}/emojis/{emoji_id}', ['guild_id' => $guildId, 'emoji_id' => $emojiId]), $data);
    }

    public function deleteGuildEmoji(int $guildId, int $emojiId): array
    {
        return $this->request(new Route('DELETE', '/guilds/{guild_id}/emojis/{emoji_id}', ['guild_id' => $guildId, 'emoji_id' => $emojiId]));
    }

    public function createGuild(array $data): array
    {
        return $this->request(new Route('POST', '/guilds'), $data);
    }

    public function getGuild(int $guildId, array $query = []): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}', ['guild_id' => $guildId]), query: $query);
    }

    public function getGuildPreview(int $guildId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/preview', ['guild_id' => $guildId]));
    }

    public function modifyGuild(int $guildId, array $data): array
    {
        return $this->request(new Route('PATCH', '/guilds/{guild_id}', ['guild_id' => $guildId]), $data);
    }

    public function deleteGuild(int $guildId): array
    {
        return $this->request(new Route('DELETE', '/guilds/{guild_id}', ['guild_id' => $guildId]));
    }

    public function getGuildChannels(int $guildId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/channels', ['guild_id' => $guildId]));
    }

    public function createGuildChannel(int $guildId, array $data): array
    {
        return $this->request(new Route('POST', '/guilds/{guild_id}/channels', ['guild_id' => $guildId]), $data);
    }

    public function modifyGuildChannelPositions(int $guildId, array $data): array
    {
        return $this->request(new Route('PATCH', '/guilds/{guild_id}/channels', ['guild_id' => $guildId]), $data);
    }

    public function listActiveGuildThreads(int $guildId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/threads/active', ['guild_id' => $guildId]));
    }

    public function getGuildMember(int $guildId, int $userId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/members/{user_id}', ['guild_id' => $guildId, 'user_id' => $userId]));
    }

    public function listGuildMembers(int $guildId, array $data = []): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/members', ['guild_id' => $guildId]), $data);
    }

    public function searchGuildMembers(int $guildId, array $query = []): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/members/search', ['guild_id' => $guildId]), query: $query);
    }

    public function addGuildMember(int $guildId, int $userId, array $data): array
    {
        return $this->request(new Route('PUT', '/guilds/{guild_id}/members/{user_id}', ['guild_id' => $guildId, 'user_id' => $userId]), $data);
    }

    public function modifyGuildMember(int $guildId, int $userId, array $data): array
    {
        return $this->request(new Route('PATCH', '/guilds/{guild_id}/members/{user_id}', ['guild_id' => $guildId, 'user_id' => $userId]), $data);
    }

    public function modifyCurrentMember(int $guildId, array $data): array
    {
        return $this->request(new Route('PATCH', '/guilds/{guild_id}/members/@me', ['guild_id' => $guildId]), $data);
    }

    public function addGuildMemberRole(int $guildId, int $userId, int $roleId): array
    {
        return $this->request(new Route('PUT', '/guilds/{guild_id}/members/{user_id}/roles/{role_id}', ['guild_id' => $guildId, 'user_id' => $userId, 'role_id' => $roleId]));
    }

    public function removeGuildMemberRole(int $guildId, int $userId, int $roleId): array
    {
        return $this->request(new Route('DELETE', '/guilds/{guild_id}/members/{user_id}/roles/{role_id}', ['guild_id' => $guildId, 'user_id' => $userId, 'role_id' => $roleId]));
    }

    public function removeGuildMember(int $guildId, int $userId): array
    {
        return $this->request(new Route('DELETE', '/guilds/{guild_id}/members/{user_id}', ['guild_id' => $guildId, 'user_id' => $userId]));
    }

    public function getGuildBans(int $guildId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/bans', ['guild_id' => $guildId]));
    }

    public function getGuildBan(int $guildId, int $userId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/bans/{user_id}', ['guild_id' => $guildId, 'user_id' => $userId]));
    }

    public function createGuildBan(int $guildId, int $userId, array $data = []): array
    {
        return $this->request(new Route('PUT', '/guilds/{guild_id}/bans/{user_id}', ['guild_id' => $guildId, 'user_id' => $userId]), $data);
    }

    public function removeGuildBan(int $guildId, int $userId): array
    {
        return $this->request(new Route('DELETE', '/guilds/{guild_id}/bans/{user_id}', ['guild_id' => $guildId, 'user_id' => $userId]));
    }

    public function getGuildRoles(int $guildId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/roles', ['guild_id' => $guildId]));
    }

    public function createGuildRole(int $guildId, array $data): array
    {
        return $this->request(new Route('POST', '/guilds/{guild_id}/roles', ['guild_id' => $guildId]), $data);
    }

    public function modifyGuildRolePositions(int $guildId, array $data): array
    {
        return $this->request(new Route('PATCH', '/guilds/{guild_id}/roles', ['guild_id' => $guildId]), $data);
    }

    public function modifyGuildRole(int $guildId, int $roleId, array $data): array
    {
        return $this->request(new Route('PATCH', '/guilds/{guild_id}/roles/{role_id}', ['guild_id' => $guildId, 'role_id' => $roleId]), $data);
    }

    public function modifyGuildMfaLevel(int $guildId, array $data): array
    {
        return $this->request(new Route('POST', '/guilds/{guild_id}/mfa', ['guild_id' => $guildId]), $data);
    }

    public function deleteGuildRole(int $guildId, int $roleId): array
    {
        return $this->request(new Route('DELETE', '/guilds/{guild_id}/roles/{role_id}', ['guild_id' => $guildId, 'role_id' => $roleId]));
    }

    public function getGuildPruneCount(int $guildId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/prune', ['guild_id' => $guildId]));
    }

    public function beginGuildPrune(int $guildId): array
    {
        return $this->request(new Route('POST', '/guilds/{guild_id}/prune', ['guild_id' => $guildId]));
    }

    public function getGuildVoiceRegions(int $guildId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/regions', ['guild_id' => $guildId]));
    }

    public function getGuildInvites(int $guildId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/invites'));
    }

    public function getGuildIntegrations(int $guildId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/integrations'));
    }

    public function deleteGuildIntegrations(int $guildId, int $integrationId): array
    {
        return $this->request(new Route('DELETE', '/guilds/{guild_id}/integrations/{integration_id}', ['guild_id' => $guildId, 'integration_id' => $integrationId]));
    }

    public function getGuildWidgetSettings(int $guildId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/widget', ['guild_id' => $guildId]));
    }

    public function modifyGuildWidget(int $guildId, array $data): array
    {
        return $this->request(new Route('PATCH', '/guilds/{guild_id}/widget', ['guild_id' => $guildId]), $data);
    }

    public function getGuildWidget(int $guildId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/widget.json', ['guild_id' => $guildId]));
    }

    public function getGuildVanityUrl(int $guildId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/vanity-url', ['guild_id' => $guildId]));
    }

    public function getGuildWidgetImage(int $guildId, array $data = []): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/widget.png', ['guild_id' => $guildId]), $data);
    }

    public function getGuildWelcomeScreen(int $guildId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/welcome-screen', ['guild_id' => $guildId]));
    }

    public function modifyGuildWelcomeScreen(int $guildId, array $data): array
    {
        return $this->request(new Route('PATCH', '/guilds/{guild_id}/welcome-screen', ['guild_id' => $guildId]), $data);
    }

    public function getGuildOnboarding(int $guildId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/onboarding', ['guild_id' => $guildId]));
    }

    public function modifyGuildOnboarding(int $guildId, array $data): array
    {
        return $this->request(new Route('PATCH', '/guilds/{guild_id}/onboarding', ['guild_id' => $guildId]), $data);
    }

    public function modifyCurrentUserVoiceState(int $guildId, array $data): array
    {
        return $this->request(new Route('PATCH', '/guilds/{guild_id}/voice-states/@me', ['guild_id' => $guildId]), $data);
    }

    public function modifyUserVoiceState(int $guildId, int $userId, array $data): array
    {
        return $this->request(new Route('PATCH', '/guilds/{guild_id}/voice-states/{user_id}', ['guild_id' => $guildId, 'user_id' => $userId]), $data);
    }

    public function listScheduledEventsForGuild(int $guildId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/events', ['guild_id' => $guildId]));
    }

    public function createGuildScheduledEvent(int $guildId, array $data): array
    {
        return $this->request(new Route('POST', '/guilds/{guild_id}/events', ['guild_id' => $guildId]), $data);
    }

    public function getGuildScheduledEvent(int $guildId, string $guildScheduledEventId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/events/{guild_scheduled_event_id}', ['guild_id' => $guildId, 'guild_scheduled_event_id' => $guildScheduledEventId]));
    }

    public function modifyGuildScheduledEvent(int $guildId, string $guildScheduledEventId, array $data): array
    {
        return $this->request(new Route('PATCH', '/guilds/{guild_id}/events/{guild_scheduled_event_id}', ['guild_id' => $guildId, 'guild_scheduled_event_id' => $guildScheduledEventId]), $data);
    }

    public function deleteGuildScheduledEvent(int $guildId, string $guildScheduledEventId): array
    {
        return $this->request(new Route('DELETE', '/guilds/{guild_id}/events/{guild_scheduled_event_id}', ['guild_id' => $guildId, 'guild_scheduled_event_id' => $guildScheduledEventId]));
    }

    public function getGuildScheduledEventUsers(int $guildId, string $guildScheduledEventId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/events/{guild_scheduled_event_id}/users', ['guild_id' => $guildId, 'guild_scheduled_event_id' => $guildScheduledEventId]));
    }

    public function getGuildTemplate(string $templateCode): array
    {
        $this->request(new Route('GET', '/guilds/templates/{template_code}', ['template_code' => $templateCode]));
    }

    public function createGuildFromTemplate(string $templateCode, array $data): array
    {
        return $this->request(new Route('POST', '/guilds/templates/{template_code}', ['template_code' => $templateCode]), $data);
    }

    public function getGuildTemplates(int $guildId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/templates', ['guild_id' => $guildId]));
    }

    public function createGuildTemplate(int $guildId, array $data): array
    {
        return $this->request(new Route('POST', '/guilds/{guild_id}/templates', ['guild_id' => $guildId]), $data);
    }

    public function syncGuildTemplate(int $guildId, string $templateCode): array
    {
        return $this->request(new Route('PUT', '/guilds/{guild_id}/templates/{template_code}', ['guild_id' => $guildId, 'template_code' => $templateCode]));
    }

    public function modifyGuildTemplate(int $guildId, string $templateCode, array $data): array
    {
        return $this->request(new Route('PATCH', '/guilds/{guild_id}/templates/{template_code}', ['guild_id' => $guildId, 'template_code' => $templateCode]), $data);
    }

    public function deleteGuildTemplate(int $guildId, string $templateCode): array
    {
        return $this->request(new Route('DELETE', '/guilds/{guild_id}/templates/{template_code}', ['guild_id' => $guildId, 'template_code' => $templateCode]));
    }

    public function getInvite(string $inviteCode, array $data = []): array
    {
        return $this->request(new Route('GET', '/invites/{invite_code}', ['invite_code' => $inviteCode]), $data);
    }

    public function deleteInvite(string $inviteCode): array
    {
        return $this->request(new Route('DELETE', '/invites/{invite_code}', ['invite_code' => $inviteCode]));
    }

    public function createStageInstance(int $channelId, array $data): array
    {
        return $this->request(new Route('POST', '/stage-instances', ['channel_id' => $channelId]), $data);
    }

    public function getStageInstance(int $channelId): array
    {
        return $this->request(new Route('GET', '/stage-instances/{channel_id}', ['channel_id' => $channelId]));
    }

    public function modifyStageInstance(int $channelId, array $data): array
    {
        return $this->request(new Route('PATCH', '/stage-instances/{channel_id}', ['channel_id' => $channelId]), $data);
    }

    public function deleteStageInstance(int $channelId): array
    {
        return $this->request(new Route('DELETE', '/stage-instances/{channel_id}', ['channel_id' => $channelId]));
    }

    public function getSticker(int $stickerId): array
    {
        return $this->request(new Route('GET', '/stickers/{sticker_id}', ['sticker_id' => $stickerId]));
    }

    public function listNitroStickerPacks(): array
    {
        return $this->request(new Route('GET', '/sticker-packs'));
    }

    public function listGuildStickers(int $guildId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/stickers', ['guild_id' => $guildId]));
    }

    public function getGuildStickers(int $guildId, int $stickerId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/stickers', ['guild_id' => $guildId]));
    }

    public function getGuildSticker(int $guildId, int $stickerId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/stickers/{sticker_id}', ['guild_id' => $guildId, 'sticker_id' => $stickerId]));
    }

    public function createGuildSticker(int $guildId, array $data): array
    {
        return $this->request(new Route('POST', '/guilds/{guild_id}/stickers', ['guild_id' => $guildId]), $data);
    }

    public function modifyGuildSticker(int $guildId, int $stickerId, array $data): array
    {
        return $this->request(new Route('PATCH', '/guilds/{guild_id}/stickers/{sticker_id}', ['guild_id' => $guildId, 'sticker_id' => $stickerId]), $data);
    }

    public function deleteGuildSticker(int $guildId, int $stickerId): array
    {
        return $this->request(new Route('DELETE', '/guilds/{guild_id}/stickers/{sticker_id}', ['guild_id' => $guildId, 'sticker_id' => $stickerId]));
    }

    public function getCurrentUser(): array
    {
        return $this->request(new Route('GET', '/users/@me'));
    }

    public function getUser(int $userId): array
    {
        return $this->request(new Route('GET', '/users/{user_id}', ['user_id' => $userId]));
    }

    public function modifyCurrentUser(array $data): array
    {
        return $this->request(new Route('PATCH', '/users/@me'), $data);
    }

    public function getCurrentUserGuilds(array $data = []): array
    {
        return $this->request(new Route('GET', '/users/@me/guilds'), $data);
    }

    public function getCurrentUserGuildMember(int $guildId): array
    {
        return $this->request(new Route('GET', '/users/@me/guilds/{guild_id}/member', ['guild_id' => $guildId]));
    }

    public function leaveGuild(int $guildId): array
    {
        return $this->request(new Route('DELETE', '/users/@me/guilds/{guild_id}', ['guild_id' => $guildId]));
    }

    public function createDm(array $data): array
    {
        return $this->request(new Route('POST', '/users/@me/channels'), $data);
    }

    public function createGroupDm(array $data): array
    {
        return $this->request(new Route('POST', '/users/@me/channels'), $data);
    }

    public function getUserConnections(): array
    {
        return $this->request(new Route('GET', '/users/@me/connections'));
    }

    public function getUserApplicationRoleConnections(int $guildId): array
    {
        return $this->request(new Route('GET', '/users/@me/connections/{guild_id}', ['guild_id' => $guildId]));
    }

    public function updateUserApplicationRoleConnections(int $guildId, array $data): array
    {
        return $this->request(new Route('PATCH', '/users/@me/connections/{guild_id}', ['guild_id' => $guildId]), $data);
    }

    public function listVoiceRegions(): array
    {
        return $this->request(new Route('GET', '/voice/regions'));
    }

    public function createWebhook(int $channelId, array $data): array
    {
        return $this->request(new Route('POST', '/channels/{channel_id}/webhooks', ['channel_id' => $channelId]), $data);
    }

    public function getChannelWebhooks(int $channelId): array
    {
        return $this->request(new Route('GET', '/channels/{channel_id}/webhooks', ['channel_id' => $channelId]));
    }

    public function getGuildWebhooks(int $guildId): array
    {
        return $this->request(new Route('GET', '/guilds/{guild_id}/webhooks', ['guild_id' => $guildId]));
    }

    public function getWebhook(int $webhookId): array
    {
        return $this->request(new Route('GET', '/webhooks/{webhook_id}', ['webhook_id' => $webhookId]));
    }

    public function getWebhookWithToken(int $webhookId, string $webhookToken): array
    {
        return $this->request(new Route('GET', '/webhooks/{webhook_id}/{webhook_token}', ['webhook_id' => $webhookId, 'webhook_token' => $webhookToken]));
    }

    public function modifyWebhook(int $webhookId, array $data): array
    {
        return $this->request(new Route('PATCH', '/webhooks/{webhook_id}', ['webhook_id' => $webhookId]), $data);
    }

    public function modifyWebhookWIthToken(int $webhookId, string $webhookToken, array $data): array
    {
        return $this->request(new Route('PATCH', '/webhooks/{webhook_id}/{webhook_token}', ['webhook_id' => $webhookId, 'webhook_token' => $webhookToken]), $data);
    }

    public function deleteWebhook(int $webhookId): array
    {
        return $this->request(new Route('DELETE', '/webhooks/{webhook_id}', ['webhook_id' => $webhookId]));
    }

    public function deleteWebhookWithToken(int $webhookId, string $webhookToken): array
    {
        return $this->request(new Route('DELETE', '/webhooks/{webhook_id}/{webhook_token}', ['webhook_id' => $webhookId, 'webhook_token' => $webhookToken]));
    }

    public function executeWebhook(int $webhookId, string $webhookToken, array $data): array
    {
        return $this->request(new Route('POST', '/webhooks/{webhook_id}/{webhook_token}', ['webhook_id' => $webhookId, 'webhook_token' => $webhookToken]), $data);
    }

    public function executeSlackCompatibleWebhook(int $webhookId, string $webhookToken, array $data): array
    {
        return $this->request(new Route('POST', '/webhooks/{webhook_id}/{webhook_token}/slack', ['webhook_id' => $webhookId, 'webhook_token' => $webhookToken]), $data);
    }

    public function executeGitHubCompatibleWebhook(int $webhookId, string $webhookToken, array $data): array
    {
        return $this->request(new Route('POST', '/webhooks/{webhook_id}/{webhook_token}/github', ['webhook_id' => $webhookId, 'webhook_token' => $webhookToken]), $data);
    }

    public function getWebhookMessage(int $webhookId, string $webhookToken, string $messageId): array
    {
        return $this->request(new Route('GET', '/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}', ['webhook_id' => $webhookId, 'webhook_token' => $webhookToken, 'message_id' => $messageId]));
    }

    public function editWebhookMessage(int $webhookId, string $webhookToken, string $messageId, array $data): array
    {
        return $this->request(new Route('PATCH', '/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}', ['webhook_id' => $webhookId, 'webhook_token' => $webhookToken, 'message_id' => $messageId]), $data);
    }

    public function deleteWebhookMessage(int $webhookId, string $webhookToken, string $messageId): array
    {
        return $this->request(new Route('DELETE', '/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}', ['webhook_id' => $webhookId, 'webhook_token' => $webhookToken, 'message_id' => $messageId]));
    }
}
