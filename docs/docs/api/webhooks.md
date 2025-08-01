# Webhooks

This section contains all methods related to Webhooks.

## createWebhook

Create webhook

```php
public static array createWebhook(int $channelId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `channelId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## getWebhook

Get webhook

```php
public static array getWebhook(int $webhookId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `webhookId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## getWebhookWithToken

Get webhook with token

```php
public static array getWebhookWithToken(int $webhookId, string $webhookToken)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `webhookId` | `int` | Yes | - | - |
| `webhookToken` | `string` | Yes | - | - |

### Returns

Returns `array`

---

## modifyWebhook

Modify webhook

```php
public static array modifyWebhook(int $webhookId, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `webhookId` | `int` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## modifyWebhookWithToken

Modify webhook with token

```php
public static array modifyWebhookWithToken(int $webhookId, string $webhookToken, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `webhookId` | `int` | Yes | - | - |
| `webhookToken` | `string` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## deleteWebhook

Delete webhook

```php
public static array deleteWebhook(int $webhookId)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `webhookId` | `int` | Yes | - | - |

### Returns

Returns `array`

---

## deleteWebhookWithToken

Delete webhook with token

```php
public static array deleteWebhookWithToken(int $webhookId, string $webhookToken)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `webhookId` | `int` | Yes | - | - |
| `webhookToken` | `string` | Yes | - | - |

### Returns

Returns `array`

---

## executeWebhook

Execute webhook

```php
public static array executeWebhook(int $webhookId, string $webhookToken, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `webhookId` | `int` | Yes | - | - |
| `webhookToken` | `string` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## executeSlackCompatibleWebhook

Execute slack compatible webhook

```php
public static array executeSlackCompatibleWebhook(int $webhookId, string $webhookToken, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `webhookId` | `int` | Yes | - | - |
| `webhookToken` | `string` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

## executeGitHubCompatibleWebhook

Execute git hub compatible webhook

```php
public static array executeGitHubCompatibleWebhook(int $webhookId, string $webhookToken, array $data)
```

### Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `webhookId` | `int` | Yes | - | - |
| `webhookToken` | `string` | Yes | - | - |
| `data` | `array` | Yes | - | - |

### Returns

Returns `array`

---

