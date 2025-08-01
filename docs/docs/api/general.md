# General

This section contains all methods related to General.

## getCurrentApplication

Get current application

```php
public static array getCurrentApplication()
```

### Returns

Returns `array`

---

## getApplicationRoleConnectionMetadataRecords

Get application role connection metadata records

```php
public static array getApplicationRoleConnectionMetadataRecords(int $applicationId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `applicationId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## updateApplicationRoleConnectionMetadataRecords

Update application role connection metadata records

```php
public static array updateApplicationRoleConnectionMetadataRecords(int $applicationId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `applicationId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## getAutoModerationRule

Get auto moderation rule

```php
public static array getAutoModerationRule(int $guildId, int $ruleId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `ruleId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## createAutoModerationRule

Create auto moderation rule

```php
public static array createAutoModerationRule(int $guildId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## modifyAutoModerationRule

Modify auto moderation rule

```php
public static array modifyAutoModerationRule(int $guildId, int $ruleId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `ruleId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## deleteAutoModerationRule

Delete auto moderation rule

```php
public static array deleteAutoModerationRule(int $guildId, int $ruleId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `ruleId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## createReaction

Create reaction

```php
public static array createReaction(int $channelId, int $messageId, string $emoji)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `messageId` | `int` | Yes | - | - |
| `emoji` | `string` | Yes | - | - |

### Returns

Returns `array`

---

## deleteOwnReaction

Delete own reaction

```php
public static array deleteOwnReaction(int $channelId, int $messageId, string $emoji)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `messageId` | `int` | Yes | - | - |
| `emoji` | `string` | Yes | - | - |

### Returns

Returns `array`

---

## getReactions

Get reactions

```php
public static array getReactions(int $channelId, int $messageId, string $emoji)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `messageId` | `int` | Yes | - | - |
| `emoji` | `string` | Yes | - | - |

### Returns

Returns `array`

---

## deleteAllReactions

Delete all reactions

```php
public static array deleteAllReactions(int $channelId, int $messageId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `messageId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## deleteAllReactionsForEmoji

Delete all reactions for emoji

```php
public static array deleteAllReactionsForEmoji(int $channelId, int $messageId, string $emoji)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `messageId` | `int` | Yes | - | - |
| `emoji` | `string` | Yes | - | - |

### Returns

Returns `array`

---

## triggerTypingIndicator

Trigger typing indicator

```php
public static array triggerTypingIndicator(int $channelId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## joinThread

Join thread

```php
public static array joinThread(int $channelId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## addThreadMember

Add thread member

```php
public static array addThreadMember(int $channelId, int $userId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `userId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## leaveThread

Leave thread

```php
public static array leaveThread(int $channelId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## removeThreadMember

Remove thread member

```php
public static array removeThreadMember(int $channelId, int $userId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `userId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getThreadMember

Get thread member

```php
public static array getThreadMember(int $channelId, int $userId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `userId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## listThreadMembers

List thread members

```php
public static array listThreadMembers(int $channelId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## listPublicArchivedThreads

List public archived threads

```php
public static array listPublicArchivedThreads(int $channelId, array $data = [])
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `data` | `array` | No | [] | - |

### Returns

Returns `array`

---

## listPrivateArchivedThreads

List private archived threads

```php
public static array listPrivateArchivedThreads(int $channelId, array $data = [])
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `data` | `array` | No | [] | - |

### Returns

Returns `array`

---

## listJoinedPrivateArchivedThreads

List joined private archived threads

```php
public static array listJoinedPrivateArchivedThreads(int $channelId, array $data = [])
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `data` | `array` | No | [] | - |

### Returns

Returns `array`

---

## modifyCurrentMember

Modify current member

```php
public static array modifyCurrentMember(int $guildId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## getInvite

Get invite

```php
public static array getInvite(string $inviteCode, array $data = [])
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `inviteCode` | `string` | Yes | - | - |
| `data` | `array` | No | [] | - |

### Returns

Returns `array`

---

## deleteInvite

Delete invite

```php
public static array deleteInvite(string $inviteCode)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `inviteCode` | `string` | Yes | - | - |

### Returns

Returns `array`

---

## createStageInstance

Create stage instance

```php
public static array createStageInstance(int $channelId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## getStageInstance

Get stage instance

```php
public static array getStageInstance(int $channelId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## modifyStageInstance

Modify stage instance

```php
public static array modifyStageInstance(int $channelId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## deleteStageInstance

Delete stage instance

```php
public static array deleteStageInstance(int $channelId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getSticker

Get sticker

```php
public static array getSticker(int $stickerId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `stickerId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## listNitroStickerPacks

List nitro sticker packs

```php
public static array listNitroStickerPacks()
```

### Returns

Returns `array`

---

## createDm

Create dm

```php
public static array createDm(array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## createGroupDm

Create group dm

```php
public static array createGroupDm(array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## listVoiceRegions

List voice regions

```php
public static array listVoiceRegions()
```

### Returns

Returns `array`

---

