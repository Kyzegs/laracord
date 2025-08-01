# Users

This section contains all methods related to Users.

## deleteUserReaction

Delete user reaction

```php
public static array deleteUserReaction(int $channelId, int $messageId, string $emoji, int $userId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `messageId` | `int` | Yes | - | - |
| `emoji` | `string` | Yes | - | - |
| `userId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## modifyCurrentUserVoiceState

Modify current user voice state

```php
public static array modifyCurrentUserVoiceState(int $guildId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## modifyUserVoiceState

Modify user voice state

```php
public static array modifyUserVoiceState(int $guildId, int $userId, array $data)
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

## getCurrentUser

Get current user

```php
public static array getCurrentUser()
```

### Returns

Returns `array`

---

## getUser

Get user

```php
public static array getUser(int $userId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `userId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## modifyCurrentUser

Modify current user

```php
public static array modifyCurrentUser(array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## getUserConnections

Get user connections

```php
public static array getUserConnections()
```

### Returns

Returns `array`

---

## getUserApplicationRoleConnections

Get user application role connections

```php
public static array getUserApplicationRoleConnections(int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## updateUserApplicationRoleConnections

Update user application role connections

```php
public static array updateUserApplicationRoleConnections(int $guildId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `guildId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

