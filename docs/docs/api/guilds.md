# Guilds

This section contains all methods related to Guilds.

## getGuildAuditLog

Get guild audit log

```php
public static array getGuildAuditLog(int $guildId, array $query = [])
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `query` | `array` | No | [] | - |

### Returns

Returns `array`

---

## listAutoModerationRulesForGuild

List auto moderation rules for guild

```php
public static array listAutoModerationRulesForGuild(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## listGuildEmojis

List guild emojis

```php
public static array listGuildEmojis(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildEmoji

Get guild emoji

```php
public static array getGuildEmoji(int $guildId, int $emojiId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `emojiId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## createGuildEmoji

Create guild emoji

```php
public static array createGuildEmoji(int $guildId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## modifyGuildEmoji

Modify guild emoji

```php
public static array modifyGuildEmoji(int $guildId, int $emojiId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `emojiId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## deleteGuildEmoji

Delete guild emoji

```php
public static array deleteGuildEmoji(int $guildId, int $emojiId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `emojiId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## createGuild

Create guild

```php
public static array createGuild(array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## getGuild

Get guild

```php
public static array getGuild(int $guildId, array $query = [])
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `query` | `array` | No | [] | - |

### Returns

Returns `array`

---

## getGuildPreview

Get guild preview

```php
public static array getGuildPreview(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## modifyGuild

Modify guild

```php
public static array modifyGuild(int $guildId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## deleteGuild

Delete guild

```php
public static array deleteGuild(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## listActiveGuildThreads

List active guild threads

```php
public static array listActiveGuildThreads(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildMember

Get guild member

```php
public static array getGuildMember(int $guildId, int $userId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `userId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## listGuildMembers

List guild members

```php
public static array listGuildMembers(int $guildId, array $data = [])
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `data` | `array` | No | [] | - |

### Returns

Returns `array`

---

## searchGuildMembers

Search guild members

```php
public static array searchGuildMembers(int $guildId, array $query = [])
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `query` | `array` | No | [] | - |

### Returns

Returns `array`

---

## addGuildMember

Add guild member

```php
public static array addGuildMember(int $guildId, int $userId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `userId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## modifyGuildMember

Modify guild member

```php
public static array modifyGuildMember(int $guildId, int $userId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `userId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## addGuildMemberRole

Add guild member role

```php
public static array addGuildMemberRole(int $guildId, int $userId, int $roleId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `userId` | `int` | Yes | - | - |
| `roleId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## removeGuildMemberRole

Remove guild member role

```php
public static array removeGuildMemberRole(int $guildId, int $userId, int $roleId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `userId` | `int` | Yes | - | - |
| `roleId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## removeGuildMember

Remove guild member

```php
public static array removeGuildMember(int $guildId, int $userId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `userId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildBans

Get guild bans

```php
public static array getGuildBans(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildBan

Get guild ban

```php
public static array getGuildBan(int $guildId, int $userId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `userId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## createGuildBan

Create guild ban

```php
public static array createGuildBan(int $guildId, int $userId, array $data = [])
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `userId` | `int` | Yes | - | - |
| `data` | `array` | No | [] | - |

### Returns

Returns `array`

---

## removeGuildBan

Remove guild ban

```php
public static array removeGuildBan(int $guildId, int $userId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `userId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildRoles

Get guild roles

```php
public static array getGuildRoles(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## createGuildRole

Create guild role

```php
public static array createGuildRole(int $guildId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## modifyGuildRolePositions

Modify guild role positions

```php
public static array modifyGuildRolePositions(int $guildId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## modifyGuildRole

Modify guild role

```php
public static array modifyGuildRole(int $guildId, int $roleId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `roleId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## modifyGuildMfaLevel

Modify guild mfa level

```php
public static array modifyGuildMfaLevel(int $guildId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## deleteGuildRole

Delete guild role

```php
public static array deleteGuildRole(int $guildId, int $roleId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `roleId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildPruneCount

Get guild prune count

```php
public static array getGuildPruneCount(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## beginGuildPrune

Begin guild prune

```php
public static array beginGuildPrune(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildVoiceRegions

Get guild voice regions

```php
public static array getGuildVoiceRegions(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildInvites

Get guild invites

```php
public static array getGuildInvites(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildIntegrations

Get guild integrations

```php
public static array getGuildIntegrations(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## deleteGuildIntegrations

Delete guild integrations

```php
public static array deleteGuildIntegrations(int $guildId, int $integrationId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `integrationId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildWidgetSettings

Get guild widget settings

```php
public static array getGuildWidgetSettings(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## modifyGuildWidget

Modify guild widget

```php
public static array modifyGuildWidget(int $guildId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildWidget

Get guild widget

```php
public static array getGuildWidget(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildVanityUrl

Get guild vanity url

```php
public static array getGuildVanityUrl(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildWidgetImage

Get guild widget image

```php
public static array getGuildWidgetImage(int $guildId, array $data = [])
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `data` | `array` | No | [] | - |

### Returns

Returns `array`

---

## getGuildWelcomeScreen

Get guild welcome screen

```php
public static array getGuildWelcomeScreen(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## modifyGuildWelcomeScreen

Modify guild welcome screen

```php
public static array modifyGuildWelcomeScreen(int $guildId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildOnboarding

Get guild onboarding

```php
public static array getGuildOnboarding(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## modifyGuildOnboarding

Modify guild onboarding

```php
public static array modifyGuildOnboarding(int $guildId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## listScheduledEventsForGuild

List scheduled events for guild

```php
public static array listScheduledEventsForGuild(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## createGuildScheduledEvent

Create guild scheduled event

```php
public static array createGuildScheduledEvent(int $guildId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildScheduledEvent

Get guild scheduled event

```php
public static array getGuildScheduledEvent(int $guildId, int $guildScheduledEventId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `guildScheduledEventId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## modifyGuildScheduledEvent

Modify guild scheduled event

```php
public static array modifyGuildScheduledEvent(int $guildId, int $guildScheduledEventId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `guildScheduledEventId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## deleteGuildScheduledEvent

Delete guild scheduled event

```php
public static array deleteGuildScheduledEvent(int $guildId, int $guildScheduledEventId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `guildScheduledEventId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildScheduledEventUsers

Get guild scheduled event users

```php
public static array getGuildScheduledEventUsers(int $guildId, int $guildScheduledEventId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `guildScheduledEventId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildTemplate

Get guild template

```php
public static array getGuildTemplate(string $templateCode)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `templateCode` | `string` | Yes | - | - |

### Returns

Returns `array`

---

## createGuildFromTemplate

Create guild from template

```php
public static array createGuildFromTemplate(string $templateCode, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `templateCode` | `string` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildTemplates

Get guild templates

```php
public static array getGuildTemplates(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## createGuildTemplate

Create guild template

```php
public static array createGuildTemplate(int $guildId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## syncGuildTemplate

Sync guild template

```php
public static array syncGuildTemplate(int $guildId, string $templateCode)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `templateCode` | `string` | Yes | - | - |

### Returns

Returns `array`

---

## modifyGuildTemplate

Modify guild template

```php
public static array modifyGuildTemplate(int $guildId, string $templateCode, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `templateCode` | `string` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## deleteGuildTemplate

Delete guild template

```php
public static array deleteGuildTemplate(int $guildId, string $templateCode)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `templateCode` | `string` | Yes | - | - |

### Returns

Returns `array`

---

## listGuildStickers

List guild stickers

```php
public static array listGuildStickers(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildStickers

Get guild stickers

```php
public static array getGuildStickers(int $guildId, int $stickerId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `stickerId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildSticker

Get guild sticker

```php
public static array getGuildSticker(int $guildId, int $stickerId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `stickerId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## createGuildSticker

Create guild sticker

```php
public static array createGuildSticker(int $guildId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## modifyGuildSticker

Modify guild sticker

```php
public static array modifyGuildSticker(int $guildId, int $stickerId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `stickerId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## deleteGuildSticker

Delete guild sticker

```php
public static array deleteGuildSticker(int $guildId, int $stickerId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `stickerId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getCurrentUserGuilds

Get current user guilds

```php
public static array getCurrentUserGuilds(array $data = [])
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `data` | `array` | No | [] | - |

### Returns

Returns `array`

---

## getCurrentUserGuildMember

Get current user guild member

```php
public static array getCurrentUserGuildMember(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## leaveGuild

Leave guild

```php
public static array leaveGuild(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildWebhooks

Get guild webhooks

```php
public static array getGuildWebhooks(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

