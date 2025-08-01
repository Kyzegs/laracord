# Channels & Messages

This section contains all methods related to Channels & Messages.

## getChannel

Get channel

```php
public static array getChannel(int $channelId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## modifyChannel

Modify channel

```php
public static array modifyChannel(int $channelId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## deleteChannel

Delete channel

```php
public static array deleteChannel(int $channelId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getChannelMessages

Get channel messages

```php
public static array getChannelMessages(int $channelId, array $data = [])
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `data` | `array` | No | [] | - |

### Returns

Returns `array`

---

## getChannelMessage

Get channel message

```php
public static array getChannelMessage(int $channelId, int $messageId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `messageId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## createMessage

Create message

```php
public static array createMessage(int $channelId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## crosspostMessage

Crosspost message

```php
public static array crosspostMessage(int $channelId, int $messageId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `messageId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## editMessage

Edit message

```php
public static array editMessage(int $channelId, int $messageId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `messageId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## deleteMessage

Delete message

```php
public static array deleteMessage(int $channelId, int $messageId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `messageId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## bulkDeleteMessages

Bulk delete messages

```php
public static array bulkDeleteMessages(int $channelId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## editChannelPermissions

Edit channel permissions

```php
public static array editChannelPermissions(int $channelId, int $overwriteId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `overwriteId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## getChannelInvites

Get channel invites

```php
public static array getChannelInvites(int $channelId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## createChannelInvite

Create channel invite

```php
public static array createChannelInvite(int $channelId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## deleteChannelPermission

Delete channel permission

```php
public static array deleteChannelPermission(int $channelId, int $overwriteId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `overwriteId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## followAnnouncementChannel

Follow announcement channel

```php
public static array followAnnouncementChannel(int $channelId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## getPinnedMessages

Get pinned messages

```php
public static array getPinnedMessages(int $channelId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## pinMessage

Pin message

```php
public static array pinMessage(int $channelId, int $messageId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `messageId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## unpinMessage

Unpin message

```php
public static array unpinMessage(int $channelId, int $messageId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `messageId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## startThreadFromMessage

Start thread from message

```php
public static array startThreadFromMessage(int $channelId, int $messageId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `messageId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## startThreadWithoutMessage

Start thread without message

```php
public static array startThreadWithoutMessage(int $channelId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## startThreadInForumChannel

Start thread in forum channel

```php
public static array startThreadInForumChannel(int $channelId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildChannels

Get guild channels

```php
public static array getGuildChannels(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## createGuildChannel

Create guild channel

```php
public static array createGuildChannel(int $guildId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## modifyGuildChannelPositions

Modify guild channel positions

```php
public static array modifyGuildChannelPositions(int $guildId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## getChannelWebhooks

Get channel webhooks

```php
public static array getChannelWebhooks(int $channelId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getWebhookMessage

Get webhook message

```php
public static array getWebhookMessage(int $webhookId, string $webhookToken, int $messageId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `webhookId` | `int` | Yes | - | - |
| `webhookToken` | `string` | Yes | - | - |
| `messageId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## editWebhookMessage

Edit webhook message

```php
public static array editWebhookMessage(int $webhookId, string $webhookToken, int $messageId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `webhookId` | `int` | Yes | - | - |
| `webhookToken` | `string` | Yes | - | - |
| `messageId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## deleteWebhookMessage

Delete webhook message

```php
public static array deleteWebhookMessage(int $webhookId, string $webhookToken, int $messageId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `webhookId` | `int` | Yes | - | - |
| `webhookToken` | `string` | Yes | - | - |
| `messageId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

