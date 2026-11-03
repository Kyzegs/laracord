# Upgrading to 2.0

## Renamed endpoints

Every edit endpoint is now named `edit*`. Replace the old name wherever you call it:

| Resource | 1.x | 2.0 |
|---|---|---|
| `guilds` | `modifyChannelPositions` | `editChannelPositions` |
| `guilds` | `modifyMember` | `editMember` |
| `guilds` | `modifyCurrentMember` | `editCurrentMember` |
| `guilds` | `modifyRolePositions` | `editRolePositions` |
| `guilds` | `modifyRole` | `editRole` |
| `guilds` | `modifyWidget` | `editWidget` |
| `guilds` | `modifyWelcomeScreen` | `editWelcomeScreen` |
| `guilds` | `modifyOnboarding` | `editOnboarding` |
| `emojis` | `modifyGuildEmoji` | `editGuildEmoji` |
| `emojis` | `modifyApplicationEmoji` | `editApplicationEmoji` |
| `stickers` | `modifyGuildSticker` | `editGuildSticker` |
| `templates` | `modifyGuildTemplate` | `editGuildTemplate` |
| `users` | `modifyCurrent` | `editCurrent` |
| `voice` | `modifyCurrentState` | `editCurrentState` |
| `voice` | `modifyState` | `editState` |
| `soundboards` | `modifyGuildSound` | `editGuildSound` |
| `roleConnections` | `updateMetadata` | `editMetadata` |
| `roleConnections` | `updateUserConnection` | `editUserConnection` |

## Removed endpoints

Discord no longer offers these, so calling them already failed:

- `guilds()->create()`, `guilds()->delete()`, `guilds()->modifyMfaLevel()`
- `templates()->createGuildFromTemplate()`
- `commands()->batchEditPermissions()`: edit each command with `editPermissions()` instead

## Corrected endpoints

These pointed at the wrong route, so any code calling them was failing:

- All of `scheduledEvents()` now use `/scheduled-events`.
- `roleConnections()->getUserConnection()` and `editUserConnection()` now take `application_id`, not `guild_id`.
- `guilds()->editOnboarding()` now sends `PUT`.
- `lobbies()->moderateMessage()` now sends `PUT` to `/moderation-metadata`.

`channels()->listPinnedMessages()`, `pinMessage()`, and `unpinMessage()` moved to Discord's current routes. `listPinnedMessages()` now returns `items` and `has_more` instead of a plain list of messages.

## Behaviour changes

- **`POST` requests are no longer repeated** after a `5xx` or a timeout, because the first attempt may have taken effect. To retry `messages()->create()` yourself, set `DiscordMessage::nonce()`. Webhooks and interaction follow-ups do not take a nonce, so retrying those can post twice.
- **All `5xx` responses are retried** for other methods and throw `DiscordServerException`. Only `500`, `502`, `504`, and `524` did before.
- **`laracord:commands:sync` refuses an empty config** unless you pass `--force`.
- **`laracord:commands:clear` asks for confirmation.** Pass `--force` in scripts.
- **`InteractionResponse::message()` and `update()` throw on a message with files.** They used to drop the files silently. Defer, then use `InteractionContext::editOriginal()` or `followup()`.
- **`DiscordWebhook::fromUrl()` rejects `/slack` and `/github` URLs**, which it used to treat as plain webhooks.
- **All Guzzle transfer failures and rate limit lock timeouts throw `DiscordTransportException`.** Before, only connection failures did, so a `catch (RequestException)` or `catch (LockTimeoutException)` around a Discord call no longer fires.
- **`RateLimitExceededException` is no longer chained** to `DiscordRateLimitException`, and Guzzle exceptions are no longer chained to `DiscordTransportException`, because both carry the request URL.
- **Socialite callbacks must carry `iss=https://discord.com`.** A missing or different value throws `InvalidStateException`. If your frontend forwards the callback to your backend, forward `iss` along with `code` and `state`.
- **Notifications with components now send `with_components`**, so Discord honours them where it used to ignore them. Interactive components still need an application-owned webhook.
- **`pool()` requests now run concurrently.** They used to go out one at a time, so they can now reach Discord in any order. Don't pool requests that depend on each other.
- **Out-of-range `laracord.rate_limit` values throw `InvalidArgumentException`** when the client is built, instead of misbehaving at request time. For example, `LARACORD_GLOBAL_REQUESTS=0` used to wait forever. Negative retries, buffers or delays, jitter outside 0 to 100, limits below 1 and windows of 0 or less are all rejected.
- **Rate limit state is re-learned once after you deploy**, because `kyzegs/guzzle-rate-limit-middleware` 1.3 changed its Discord route keys. Expect a brief return to cautious, one-at-a-time requests per route.
- **Laracord requires `kyzegs/guzzle-rate-limit-middleware` 1.3 and Guzzle 7.15.2 or later.** Rate limit log messages name the route template, such as `POST /api/v10/webhooks/{webhook_id}/{webhook_token}`, rather than the full URL, so update anything that parses them.
- **`Laracord::fake()` throws on a missing route parameter**, as the real client does, so a test that left one out now fails.
- **`UnhandledInteractionException` extends `DiscordException`.** A `catch (DiscordException)` around your router now catches it too.
- **The default User-Agent reports the installed version.** `laracord.user_agent` defaults to `null`; keep setting it to override.
- **Socialite calls follow `laracord.api_url` and `laracord.api_version`** instead of always using `discord.com/api/v10`.

## Removed classes and methods

- `Kyzegs\Laracord\Laracord`: use the `Kyzegs\Laracord\Facades\Laracord` facade.
- The v0-to-v1 Rector set (`Kyzegs\Laracord\Rector\*`, `config/rector`). To migrate from 0.x, run it from 1.x first.
- `Socialite\ProviderInterface`; `Socialite\User::getGuilds()` and `setGuilds()`, which nothing in the package called: use `DiscordProvider::guildsFromToken()`.
- `Socialite\PartialGuild::hasAnyPermissions()` and `role()`.
- `InteractionResponse::webhookPong()`: use `WebhookEventRouter`.
- `DiscordMessage::IS_COMPONENTS_V2`: use `Constants\MessageFlags::IS_COMPONENTS_V2`.
- `Permissions::MANAGE_EMOJIS_AND_STICKERS`: use `MANAGE_GUILD_EXPRESSIONS`.
- `Permissions::START_EMBEDDED_ACTIVITIES`: use `USE_EMBEDDED_ACTIVITIES`.
- `Authentication::fingerprint()`, `Modal::customId()`, `EndpointCatalog::AUDITED_AT`.

## New implementations of `Contracts\Factory`

`Contracts\Factory` gains `interactions()`. Add it if you implement that interface yourself.
