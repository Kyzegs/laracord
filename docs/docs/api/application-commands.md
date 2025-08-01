# Application Commands

This section contains all methods related to Application Commands.

## getGlobalApplicationCommands

Retrieve all global application commands for an application.

```php
public static array getGlobalApplicationCommands(int $applicationId, array $query = [])
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `applicationId` | `int` | Yes | - | The ID of the application to retrieve commands for. |
| `query` | `array` | No | [] | Optional query parameters. |

### Returns

Returns `array`

---

## createGlobalApplicationCommand

Create global application command

```php
public static array createGlobalApplicationCommand(int $applicationId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `applicationId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## getGlobalApplicationCommand

Get global application command

```php
public static array getGlobalApplicationCommand(int $applicationId, int $commandId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `applicationId` | `int` | Yes | - | - |
| `commandId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## editGlobalApplicationCommand

Edit global application command

```php
public static array editGlobalApplicationCommand(int $applicationId, int $commandId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `applicationId` | `int` | Yes | - | - |
| `commandId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## deleteGlobalApplicationCommand

Delete global application command

```php
public static array deleteGlobalApplicationCommand(int $applicationId, int $commandId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `applicationId` | `int` | Yes | - | - |
| `commandId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## bulkOverwriteGlobalApplicationCommands

Bulk overwrite global application commands

```php
public static array bulkOverwriteGlobalApplicationCommands(int $applicationId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `applicationId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildApplicationCommands

Get guild application commands

```php
public static array getGuildApplicationCommands(int $applicationId, int $guildId, array $query = [])
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `applicationId` | `int` | Yes | - | - |
| `guildId` | `int` | Yes | - | - |
| `query` | `array` | No | [] | - |

### Returns

Returns `array`

---

## createGuildApplicationCommand

Create guild application command

```php
public static array createGuildApplicationCommand(int $applicationId, int $guildId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `applicationId` | `int` | Yes | - | - |
| `guildId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildApplicationCommand

Get guild application command

```php
public static array getGuildApplicationCommand(int $applicationId, int $guildId, int $commandId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `applicationId` | `int` | Yes | - | - |
| `guildId` | `int` | Yes | - | - |
| `commandId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## editGuildApplicationCommand

Edit guild application command

```php
public static array editGuildApplicationCommand(int $applicationId, int $guildId, int $commandId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `applicationId` | `int` | Yes | - | - |
| `guildId` | `int` | Yes | - | - |
| `commandId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## deleteGuildApplicationCommand

Delete guild application command

```php
public static array deleteGuildApplicationCommand(int $applicationId, int $guildId, int $commandId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `applicationId` | `int` | Yes | - | - |
| `guildId` | `int` | Yes | - | - |
| `commandId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## bulkOverwriteGuildApplicationCommands

Bulk overwrite guild application commands

```php
public static array bulkOverwriteGuildApplicationCommands(int $applicationId, int $guildId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `applicationId` | `int` | Yes | - | - |
| `guildId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## getGuildApplicationCommandPermissions

Get guild application command permissions

```php
public static array getGuildApplicationCommandPermissions(int $applicationId, int $guildId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `applicationId` | `int` | Yes | - | - |
| `guildId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getApplicationCommandPermissions

Get application command permissions

```php
public static array getApplicationCommandPermissions(int $applicationId, int $guildId, int $commandId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `applicationId` | `int` | Yes | - | - |
| `guildId` | `int` | Yes | - | - |
| `commandId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## editApplicationCommandPermissions

Edit application command permissions

```php
public static array editApplicationCommandPermissions(int $applicationId, int $guildId, int $commandId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `applicationId` | `int` | Yes | - | - |
| `guildId` | `int` | Yes | - | - |
| `commandId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## batchEditApplicationCommandPermissions

Batch edit application command permissions

```php
public static array batchEditApplicationCommandPermissions(int $applicationId, int $guildId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `applicationId` | `int` | Yes | - | - |
| `guildId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

