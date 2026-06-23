<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Rector;

use Illuminate\Support\Str;
use Kyzegs\Laracord\Endpoints\EndpointCatalog;
use Kyzegs\Laracord\Enums\HttpMethod;
use Kyzegs\Laracord\Facades\Laracord;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Rewrites Laracord 0.x calls into the 1.x resource-client API exposed by the
 * `Facades\Laracord` facade. Two 0.x calling styles are handled:
 *
 *   1. The low-level Http facade + Routes constant:
 *      Http::post(sprintf(Routes::CREATE_GLOBAL_APPLICATION_COMMAND, $appId), $body)
 *
 *   2. The flat facade endpoint methods:
 *      Laracord::createGlobalApplicationCommand($appId, $body)
 *      Laracord::getChannel($channelId)
 *
 * Both become:
 *      Laracord::bot()->commands()->createGlobalApplicationCommand(['application_id' => $appId], $body)
 *
 * The rule carries the full 0.x endpoint surface and maps each legacy name onto the
 * 1.x EndpointCatalog by its (verb, path) identity, so renamed endpoints are handled
 * too (e.g. `getChannel` -> `channels()->get()`). The trailing argument of a 0.x call
 * is treated as a query for GET endpoints and as a request body for every other verb,
 * matching the 0.x signatures.
 */
final class HttpFacadeCallToLaracordRector extends AbstractRector
{
    /**
     * Old low-level `Http` facade FQCN (removed in 1.x).
     */
    private const OLD_HTTP_FACADE = 'Kyzegs\\Laracord\\Client\\Http';

    /**
     * Old `Routes` constants holder (removed in 1.x).
     */
    private const OLD_ROUTES = 'Kyzegs\\Laracord\\Constants\\Routes';

    /**
     * The `Laracord` facade FQCN. The class name is unchanged between 0.x (flat
     * endpoint methods) and 1.x (auth-context entry points).
     */
    private const FACADE = Laracord::class;

    /**
     * Old verbs that carried a URL + body/query and are now endpoint methods.
     */
    private const HTTP_VERBS = ['get', 'post', 'put', 'patch', 'delete'];

    /**
     * 1.x facade methods that must never be treated as 0.x endpoint calls.
     */
    private const FACADE_ENTRY_POINTS = ['bot', 'bearer', 'withoutAuthentication'];

    /**
     * The complete 0.x endpoint surface as legacyName => [httpMethod, path].
     *
     * This is upgrade-only data: it mirrors the route surface 0.x shipped, so the
     * rule can recognize old flat-facade method names and Routes constants and map
     * them onto the 1.x catalog by their (verb, path) identity.
     *
     * @var array<string, array{0: string, 1: string}>
     */
    private const LEGACY_ENDPOINTS = [
        'addGuildMember' => ['PUT', '/guilds/{guild_id}/members/{user_id}'],
        'addGuildMemberRole' => ['PUT', '/guilds/{guild_id}/members/{user_id}/roles/{role_id}'],
        'addThreadMember' => ['PUT', '/channels/{channel_id}/thread-members/{user_id}'],
        'batchEditApplicationCommandPermissions' => ['PUT', '/applications/{application_id}/guilds/{guild_id}/commands/permissions'],
        'beginGuildPrune' => ['POST', '/guilds/{guild_id}/prune'],
        'bulkDeleteMessages' => ['POST', '/channels/{channel_id}/messages/bulk-delete'],
        'bulkOverwriteGlobalApplicationCommands' => ['PUT', '/applications/{application_id}/commands'],
        'bulkOverwriteGuildApplicationCommands' => ['PUT', '/applications/{application_id}/guilds/{guild_id}/commands'],
        'createAutoModerationRule' => ['POST', '/guilds/{guild_id}/auto-moderation/rules'],
        'createChannelInvite' => ['POST', '/channels/{channel_id}/invites'],
        'createDm' => ['POST', '/users/@me/channels'],
        'createGlobalApplicationCommand' => ['POST', '/applications/{application_id}/commands'],
        'createGroupDm' => ['POST', '/users/@me/channels'],
        'createGuild' => ['POST', '/guilds'],
        'createGuildApplicationCommand' => ['POST', '/applications/{application_id}/guilds/{guild_id}/commands'],
        'createGuildBan' => ['PUT', '/guilds/{guild_id}/bans/{user_id}'],
        'createGuildChannel' => ['POST', '/guilds/{guild_id}/channels'],
        'createGuildEmoji' => ['POST', '/guilds/{guild_id}/emojis'],
        'createGuildFromTemplate' => ['POST', '/guilds/templates/{template_code}'],
        'createGuildRole' => ['POST', '/guilds/{guild_id}/roles'],
        'createGuildScheduledEvent' => ['POST', '/guilds/{guild_id}/events'],
        'createGuildSticker' => ['POST', '/guilds/{guild_id}/stickers'],
        'createGuildTemplate' => ['POST', '/guilds/{guild_id}/templates'],
        'createMessage' => ['POST', '/channels/{channel_id}/messages'],
        'createReaction' => ['PUT', '/channels/{channel_id}/messages/{message_id}/reactions/{emoji}/@me'],
        'createStageInstance' => ['POST', '/stage-instances'],
        'createWebhook' => ['POST', '/channels/{channel_id}/webhooks'],
        'crosspostMessage' => ['POST', '/channels/{channel_id}/messages/{message_id}/crosspost'],
        'deleteAllReactions' => ['DELETE', '/channels/{channel_id}/messages/{message_id}/reactions'],
        'deleteAllReactionsForEmoji' => ['DELETE', '/channels/{channel_id}/messages/{message_id}/reactions/{emoji}'],
        'deleteAutoModerationRule' => ['DELETE', '/guilds/{guild_id}/auto-moderation/rules/{auto_moderation_rule_id}'],
        'deleteChannel' => ['DELETE', '/channels/{channel_id}'],
        'deleteChannelPermission' => ['DELETE', '/channels/{channel_id}/permissions/{overwrite_id}'],
        'deleteGlobalApplicationCommand' => ['DELETE', '/applications/{application_id}/commands/{command_id}'],
        'deleteGuild' => ['DELETE', '/guilds/{guild_id}'],
        'deleteGuildApplicationCommand' => ['DELETE', '/applications/{application_id}/guilds/{guild_id}/commands/{command_id}'],
        'deleteGuildEmoji' => ['DELETE', '/guilds/{guild_id}/emojis/{emoji_id}'],
        'deleteGuildIntegrations' => ['DELETE', '/guilds/{guild_id}/integrations/{integration_id}'],
        'deleteGuildRole' => ['DELETE', '/guilds/{guild_id}/roles/{role_id}'],
        'deleteGuildScheduledEvent' => ['DELETE', '/guilds/{guild_id}/events/{guild_scheduled_event_id}'],
        'deleteGuildSticker' => ['DELETE', '/guilds/{guild_id}/stickers/{sticker_id}'],
        'deleteGuildTemplate' => ['DELETE', '/guilds/{guild_id}/templates/{template_code}'],
        'deleteInvite' => ['DELETE', '/invites/{invite_code}'],
        'deleteMessage' => ['DELETE', '/channels/{channel_id}/messages/{message_id}'],
        'deleteOwnReaction' => ['DELETE', '/channels/{channel_id}/messages/{message_id}/reactions/{emoji}/@me'],
        'deleteStageInstance' => ['DELETE', '/stage-instances/{channel_id}'],
        'deleteUserReaction' => ['DELETE', '/channels/{channel_id}/messages/{message_id}/reactions/{emoji}/{user_id}'],
        'deleteWebhook' => ['DELETE', '/webhooks/{webhook_id}'],
        'deleteWebhookMessage' => ['DELETE', '/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}'],
        'deleteWebhookWithToken' => ['DELETE', '/webhooks/{webhook_id}/{webhook_token}'],
        'editApplicationCommandPermissions' => ['PUT', '/applications/{application_id}/guilds/{guild_id}/commands/{command_id}/permissions'],
        'editChannelPermissions' => ['PUT', '/channels/{channel_id}/permissions/{overwrite_id}'],
        'editGlobalApplicationCommand' => ['PATCH', '/applications/{application_id}/commands/{command_id}'],
        'editGuildApplicationCommand' => ['PATCH', '/applications/{application_id}/guilds/{guild_id}/commands/{command_id}'],
        'editMessage' => ['PATCH', '/channels/{channel_id}/messages/{message_id}'],
        'editWebhookMessage' => ['PATCH', '/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}'],
        'executeGitHubCompatibleWebhook' => ['POST', '/webhooks/{webhook_id}/{webhook_token}/github'],
        'executeSlackCompatibleWebhook' => ['POST', '/webhooks/{webhook_id}/{webhook_token}/slack'],
        'executeWebhook' => ['POST', '/webhooks/{webhook_id}/{webhook_token}'],
        'followAnnouncementChannel' => ['POST', '/channels/{channel_id}/followers'],
        'getApplicationCommandPermissions' => ['GET', '/applications/{application_id}/guilds/{guild_id}/commands/{command_id}/permissions'],
        'getApplicationRoleConnectionMetadataRecords' => ['GET', '/applications/{application_id}/role-connections/metadata'],
        'getAutoModerationRule' => ['GET', '/guilds/{guild_id}/auto-moderation/rules/{auto_moderation_rule_id}'],
        'getChannel' => ['GET', '/channels/{channel_id}'],
        'getChannelInvites' => ['GET', '/channels/{channel_id}/invites'],
        'getChannelMessage' => ['GET', '/channels/{channel_id}/messages/{message_id}'],
        'getChannelMessages' => ['GET', '/channels/{channel_id}/messages'],
        'getChannelWebhooks' => ['GET', '/channels/{channel_id}/webhooks'],
        'getCurrentApplication' => ['GET', '/applications/@me'],
        'getCurrentUser' => ['GET', '/users/@me'],
        'getCurrentUserGuildMember' => ['GET', '/users/@me/guilds/{guild_id}/member'],
        'getCurrentUserGuilds' => ['GET', '/users/@me/guilds'],
        'getGlobalApplicationCommand' => ['GET', '/applications/{application_id}/commands/{command_id}'],
        'getGlobalApplicationCommands' => ['GET', '/applications/{application_id}/commands'],
        'getGuild' => ['GET', '/guilds/{guild_id}'],
        'getGuildApplicationCommand' => ['GET', '/applications/{application_id}/guilds/{guild_id}/commands/{command_id}'],
        'getGuildApplicationCommandPermissions' => ['GET', '/applications/{application_id}/guilds/{guild_id}/commands/permissions'],
        'getGuildApplicationCommands' => ['GET', '/applications/{application_id}/guilds/{guild_id}/commands'],
        'getGuildAuditLog' => ['GET', '/guilds/{guild_id}/audit-logs'],
        'getGuildBan' => ['GET', '/guilds/{guild_id}/bans/{user_id}'],
        'getGuildBans' => ['GET', '/guilds/{guild_id}/bans'],
        'getGuildChannels' => ['GET', '/guilds/{guild_id}/channels'],
        'getGuildEmoji' => ['GET', '/guilds/{guild_id}/emojis/{emoji_id}'],
        'getGuildIntegrations' => ['GET', '/guilds/{guild_id}/integrations'],
        'getGuildInvites' => ['GET', '/guilds/{guild_id}/invites'],
        'getGuildMember' => ['GET', '/guilds/{guild_id}/members/{user_id}'],
        'getGuildOnboarding' => ['GET', '/guilds/{guild_id}/onboarding'],
        'getGuildPreview' => ['GET', '/guilds/{guild_id}/preview'],
        'getGuildPruneCount' => ['GET', '/guilds/{guild_id}/prune'],
        'getGuildRoles' => ['GET', '/guilds/{guild_id}/roles'],
        'getGuildScheduledEvent' => ['GET', '/guilds/{guild_id}/events/{guild_scheduled_event_id}'],
        'getGuildScheduledEventUsers' => ['GET', '/guilds/{guild_id}/events/{guild_scheduled_event_id}/users'],
        'getGuildSticker' => ['GET', '/guilds/{guild_id}/stickers/{sticker_id}'],
        'getGuildStickers' => ['GET', '/guilds/{guild_id}/stickers'],
        'getGuildTemplate' => ['GET', '/guilds/templates/{template_code}'],
        'getGuildTemplates' => ['GET', '/guilds/{guild_id}/templates'],
        'getGuildVanityUrl' => ['GET', '/guilds/{guild_id}/vanity-url'],
        'getGuildVoiceRegions' => ['GET', '/guilds/{guild_id}/regions'],
        'getGuildWebhooks' => ['GET', '/guilds/{guild_id}/webhooks'],
        'getGuildWelcomeScreen' => ['GET', '/guilds/{guild_id}/welcome-screen'],
        'getGuildWidget' => ['GET', '/guilds/{guild_id}/widget.json'],
        'getGuildWidgetImage' => ['GET', '/guilds/{guild_id}/widget.png'],
        'getGuildWidgetSettings' => ['GET', '/guilds/{guild_id}/widget'],
        'getInvite' => ['GET', '/invites/{invite_code}'],
        'getPinnedMessages' => ['GET', '/channels/{channel_id}/pins'],
        'getReactions' => ['GET', '/channels/{channel_id}/messages/{message_id}/reactions/{emoji}'],
        'getStageInstance' => ['GET', '/stage-instances/{channel_id}'],
        'getSticker' => ['GET', '/stickers/{sticker_id}'],
        'getThreadMember' => ['GET', '/channels/{channel_id}/thread-members/{user_id}'],
        'getUser' => ['GET', '/users/{user_id}'],
        'getUserApplicationRoleConnections' => ['GET', '/users/@me/connections/{guild_id}'],
        'getUserConnections' => ['GET', '/users/@me/connections'],
        'getWebhook' => ['GET', '/webhooks/{webhook_id}'],
        'getWebhookMessage' => ['GET', '/webhooks/{webhook_id}/{webhook_token}/messages/{message_id}'],
        'getWebhookWithToken' => ['GET', '/webhooks/{webhook_id}/{webhook_token}'],
        'joinThread' => ['PUT', '/channels/{channel_id}/thread-members/@me'],
        'leaveGuild' => ['DELETE', '/users/@me/guilds/{guild_id}'],
        'leaveThread' => ['DELETE', '/channels/{channel_id}/thread-members/@me'],
        'listActiveGuildThreads' => ['GET', '/guilds/{guild_id}/threads/active'],
        'listAutoModerationRulesForGuild' => ['GET', '/guilds/{guild_id}/auto-moderation/rules'],
        'listGuildEmojis' => ['GET', '/guilds/{guild_id}/emojis'],
        'listGuildMembers' => ['GET', '/guilds/{guild_id}/members'],
        'listGuildStickers' => ['GET', '/guilds/{guild_id}/stickers'],
        'listJoinedPrivateArchivedThreads' => ['GET', '/channels/{channel_id}/users/@me/threads/archived/private'],
        'listNitroStickerPacks' => ['GET', '/sticker-packs'],
        'listPrivateArchivedThreads' => ['GET', '/channels/{channel_id}/threads/archived/private'],
        'listPublicArchivedThreads' => ['GET', '/channels/{channel_id}/threads/archived/public'],
        'listScheduledEventsForGuild' => ['GET', '/guilds/{guild_id}/events'],
        'listThreadMembers' => ['GET', '/channels/{channel_id}/thread-members'],
        'listVoiceRegions' => ['GET', '/voice/regions'],
        'modifyAutoModerationRule' => ['PATCH', '/guilds/{guild_id}/auto-moderation/rules/{auto_moderation_rule_id}'],
        'modifyChannel' => ['PATCH', '/channels/{channel_id}'],
        'modifyCurrentMember' => ['PATCH', '/guilds/{guild_id}/members/@me'],
        'modifyCurrentUser' => ['PATCH', '/users/@me'],
        'modifyCurrentUserVoiceState' => ['PATCH', '/guilds/{guild_id}/voice-states/@me'],
        'modifyGuild' => ['PATCH', '/guilds/{guild_id}'],
        'modifyGuildChannelPositions' => ['PATCH', '/guilds/{guild_id}/channels'],
        'modifyGuildEmoji' => ['PATCH', '/guilds/{guild_id}/emojis/{emoji_id}'],
        'modifyGuildMember' => ['PATCH', '/guilds/{guild_id}/members/{user_id}'],
        'modifyGuildMfaLevel' => ['POST', '/guilds/{guild_id}/mfa'],
        'modifyGuildOnboarding' => ['PATCH', '/guilds/{guild_id}/onboarding'],
        'modifyGuildRole' => ['PATCH', '/guilds/{guild_id}/roles/{role_id}'],
        'modifyGuildRolePositions' => ['PATCH', '/guilds/{guild_id}/roles'],
        'modifyGuildScheduledEvent' => ['PATCH', '/guilds/{guild_id}/events/{guild_scheduled_event_id}'],
        'modifyGuildSticker' => ['PATCH', '/guilds/{guild_id}/stickers/{sticker_id}'],
        'modifyGuildTemplate' => ['PATCH', '/guilds/{guild_id}/templates/{template_code}'],
        'modifyGuildWelcomeScreen' => ['PATCH', '/guilds/{guild_id}/welcome-screen'],
        'modifyGuildWidget' => ['PATCH', '/guilds/{guild_id}/widget'],
        'modifyStageInstance' => ['PATCH', '/stage-instances/{channel_id}'],
        'modifyUserVoiceState' => ['PATCH', '/guilds/{guild_id}/voice-states/{user_id}'],
        'modifyWebhook' => ['PATCH', '/webhooks/{webhook_id}'],
        'modifyWebhookWithToken' => ['PATCH', '/webhooks/{webhook_id}/{webhook_token}'],
        'pinMessage' => ['PUT', '/channels/{channel_id}/pins/{message_id}'],
        'removeGuildBan' => ['DELETE', '/guilds/{guild_id}/bans/{user_id}'],
        'removeGuildMember' => ['DELETE', '/guilds/{guild_id}/members/{user_id}'],
        'removeGuildMemberRole' => ['DELETE', '/guilds/{guild_id}/members/{user_id}/roles/{role_id}'],
        'removeThreadMember' => ['DELETE', '/channels/{channel_id}/thread-members/{user_id}'],
        'searchGuildMembers' => ['GET', '/guilds/{guild_id}/members/search'],
        'startThreadFromMessage' => ['POST', '/channels/{channel_id}/messages/{message_id}/threads'],
        'startThreadInForumChannel' => ['POST', '/channels/{channel_id}/threads'],
        'startThreadWithoutMessage' => ['POST', '/channels/{channel_id}/threads'],
        'syncGuildTemplate' => ['PUT', '/guilds/{guild_id}/templates/{template_code}'],
        'triggerTypingIndicator' => ['POST', '/channels/{channel_id}/typing'],
        'unpinMessage' => ['DELETE', '/channels/{channel_id}/pins/{message_id}'],
        'updateApplicationRoleConnectionMetadataRecords' => ['PUT', '/applications/{application_id}/role-connections/metadata'],
        'updateUserApplicationRoleConnections' => ['PATCH', '/users/@me/connections/{guild_id}'],
    ];

    /**
     * Endpoint index keyed by method name: method => [resource, method, placeholders, isGet].
     *
     * @var array<string, array{0: string, 1: string, 2: list<string>, 3: bool}>|null
     */
    private ?array $byMethod = null;

    /**
     * Old `Routes` constant name => endpoint method name.
     *
     * @var array<string, string>|null
     */
    private ?array $byConst = null;

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Convert Laracord 0.x Http facade, Routes constant and flat facade calls to the 1.x resource-client API.',
            [new CodeSample(
                <<<'CODE_SAMPLE'
use Kyzegs\Laracord\Facades\Laracord;

Laracord::createMessage($channelId, ['content' => 'Hello']);
Laracord::getChannel($channelId);
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
use Kyzegs\Laracord\Facades\Laracord;

Laracord::bot()->messages()->createMessage(['channel_id' => $channelId], ['content' => 'Hello']);
Laracord::bot()->channels()->getChannel(['channel_id' => $channelId]);
CODE_SAMPLE
            )]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    /**
     * @param  StaticCall  $node
     */
    public function refactor(Node $node): ?Node
    {
        $resolved = $this->resolveCall($node);
        if ($resolved === null) {
            return null;
        }

        [$resource, $method, $placeholders, $isGet, $values, $trailing] = $resolved;

        // The number of supplied route values must line up with the placeholders;
        // bail out otherwise so we never emit a broken call.
        if (count($values) !== count($placeholders)) {
            return null;
        }

        $methodCall = new MethodCall(
            new StaticCall(new FullyQualified(self::FACADE), 'bot'),
            $resource,
        );

        return new MethodCall($methodCall, $method, $this->buildCallArgs($placeholders, $values, $isGet, $trailing));
    }

    /**
     * Resolve a 0.x call node to a normalized description, or null when the node is
     * not a recognizable 0.x endpoint call.
     *
     * @return array{0: string, 1: string, 2: list<string>, 3: bool, 4: list<Expr>, 5: Expr|null}|null
     */
    private function resolveCall(StaticCall $staticCall): ?array
    {
        // Form 1: Http::<verb>(sprintf(Routes::CONST, ...values), $trailing?)
        if ($this->isName($staticCall->class, self::OLD_HTTP_FACADE) && $this->isNames($staticCall->name, self::HTTP_VERBS)) {
            $args = $staticCall->getArgs();
            if ($args === [] || $this->hasNamedArgument($args)) {
                return null;
            }

            $route = $this->resolveRoute($args[0]->value);
            if ($route === null) {
                return null;
            }

            [$constName, $values] = $route;

            $method = $this->byConst()[$constName] ?? null;
            if ($method === null) {
                return null;
            }

            [$resource, $endpoint, $placeholders, $isGet] = $this->byMethod()[$method];

            return [$resource, $endpoint, $placeholders, $isGet, $values, $args[1]->value ?? null];
        }

        // Form 2: Laracord::<endpoint>(...values, $trailing?)
        if ($this->isName($staticCall->class, self::FACADE)) {
            $methodName = $this->getName($staticCall->name);
            if ($methodName === null || in_array($methodName, self::FACADE_ENTRY_POINTS, true)) {
                return null;
            }

            $endpoint = $this->byMethod()[$methodName] ?? null;
            if ($endpoint === null) {
                return null;
            }

            [$resource, $method, $placeholders, $isGet] = $endpoint;

            $args = $staticCall->getArgs();
            if ($this->hasNamedArgument($args)) {
                return null;
            }

            $placeholderCount = count($placeholders);
            $argCount = count($args);

            // A flat call is N placeholder values, optionally followed by one
            // body/query argument. Anything else is too ambiguous to rewrite.
            if ($argCount !== $placeholderCount && $argCount !== $placeholderCount + 1) {
                return null;
            }

            $values = [];
            for ($i = 0; $i < $placeholderCount; $i++) {
                $values[] = $args[$i]->value;
            }

            return [$resource, $method, $placeholders, $isGet, $values, $args[$placeholderCount]->value ?? null];
        }

        return null;
    }

    /**
     * Build the argument list for the new resource-client call.
     *
     * @param  list<string>  $placeholders
     * @param  list<Expr>  $values
     * @return list<Arg>
     */
    private function buildCallArgs(array $placeholders, array $values, bool $isGet, ?Expr $expr): array
    {
        $array = $this->buildParameterArray($placeholders, $values);

        if ($expr instanceof Expr) {
            // GET endpoints take a query as the third argument (body stays null);
            // every other verb takes the trailing argument as the request body.
            return $isGet
                ? [new Arg($array), new Arg(new ConstFetch(new Name('null'))), new Arg($expr)]
                : [new Arg($array), new Arg($expr)];
        }

        return $placeholders === [] ? [] : [new Arg($array)];
    }

    /**
     * Pull the `Routes::` constant name and trailing sprintf() values from the first
     * argument of an Http verb call. Supports both:
     *   - sprintf(Routes::CONST, $a, $b)
     *   - Routes::CONST (placeholder-free routes)
     *
     * @return array{0: string, 1: list<Expr>}|null
     */
    private function resolveRoute(Node $expr): ?array
    {
        if ($expr instanceof FuncCall && $this->isName($expr->name, 'sprintf')) {
            $sprintfArgs = $expr->getArgs();
            $format = $sprintfArgs[0]->value ?? null;

            if (! $format instanceof ClassConstFetch) {
                return null;
            }

            $constName = $this->resolveRoutesConstName($format);
            if ($constName === null) {
                return null;
            }

            $values = [];
            foreach (array_slice($sprintfArgs, 1) as $arg) {
                $values[] = $arg->value;
            }

            return [$constName, $values];
        }

        if ($expr instanceof ClassConstFetch) {
            $constName = $this->resolveRoutesConstName($expr);

            return $constName === null ? null : [$constName, []];
        }

        return null;
    }

    /**
     * Return the constant name when the fetch targets the old `Routes` holder.
     */
    private function resolveRoutesConstName(ClassConstFetch $classConstFetch): ?string
    {
        if (! $this->isName($classConstFetch->class, self::OLD_ROUTES)) {
            return null;
        }

        if (! $classConstFetch->name instanceof Node\Identifier) {
            return null;
        }

        return $classConstFetch->name->toString();
    }

    /**
     * Build `['application_id' => $a, 'guild_id' => $b, ...]` from ordered placeholders.
     *
     * @param  list<string>  $placeholders
     * @param  list<Expr>  $values
     */
    private function buildParameterArray(array $placeholders, array $values): Array_
    {
        $items = [];
        foreach ($placeholders as $index => $name) {
            $items[] = new ArrayItem($values[$index], new String_($name));
        }

        return new Array_($items);
    }

    /**
     * @param  array<int, Arg>  $args
     */
    private function hasNamedArgument(array $args): bool
    {
        foreach ($args as $arg) {
            if ($arg->name !== null) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: list<string>, 3: bool}>
     */
    private function byMethod(): array
    {
        return $this->byMethod ??= $this->buildIndex()['byMethod'];
    }

    /**
     * @return array<string, string>
     */
    private function byConst(): array
    {
        return $this->byConst ??= $this->buildIndex()['byConst'];
    }

    /**
     * Build (and memoize) the endpoint indexes that translate the 0.x surface to the
     * 1.x catalog.
     *
     * The 0.x legacy names are stable upgrade data carried by this rule. The 1.x
     * catalog is keyed here by "VERB path", which is the one identity Discord never
     * changes, so a legacy name resolves to its canonical resource/method regardless
     * of how it was renamed (e.g. `getChannel` -> `channels.get`).
     *
     * @return array{byMethod: array<string, array{0: string, 1: string, 2: list<string>, 3: bool}>, byConst: array<string, string>}
     */
    private function buildIndex(): array
    {
        $canonical = [];
        foreach (EndpointCatalog::all() as $resource => $endpoints) {
            foreach ($endpoints as $method => $definition) {
                $canonical[$definition['method']->value.' '.$definition['path']] = [$resource, $method];
            }
        }

        $byMethod = [];
        $byConst = [];

        foreach (self::LEGACY_ENDPOINTS as $legacyName => [$verb, $path]) {
            $canonicalEndpoint = $canonical[$verb.' '.$path] ?? null;
            if ($canonicalEndpoint === null) {
                continue;
            }

            [$resource, $method] = $canonicalEndpoint;

            preg_match_all('/\{([^}]+)}/', $path, $matches);

            $byMethod[$legacyName] = [$resource, $method, $matches[1], $verb === HttpMethod::GET->value];
            $byConst[strtoupper(Str::snake($legacyName))] = $legacyName;
        }

        $this->byMethod = $byMethod;
        $this->byConst = $byConst;

        return ['byMethod' => $byMethod, 'byConst' => $byConst];
    }
}
