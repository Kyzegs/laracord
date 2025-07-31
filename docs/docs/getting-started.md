# Getting Started

Welcome to Laracord, a powerful Laravel package for interacting with the Discord API. Laracord provides a clean, Laravel-style interface to Discord's REST API, making it easy to build Discord bots and integrations.

## What is Laracord?

Laracord is a Laravel package that wraps Discord's REST API in a familiar Laravel facade pattern. It provides:

- **Laravel-style API**: Use familiar Laravel patterns and conventions
- **Type-safe methods**: All methods are properly typed with PHP 8+ features
- **Error handling**: Built-in error handling and retry logic
- **Rate limiting**: Automatic rate limiting and backoff strategies
- **Middleware support**: Extensible middleware system for custom logic

## Quick Example

```php
use Kyzegs\Laracord\Facades\Laracord;

// Get a channel
$channel = Laracord::getChannel(123456789);

// Create a message
$message = Laracord::createMessage(123456789, [
    'content' => 'Hello, Discord!'
]);

// Get guild information
$guild = Laracord::getGuild(987654321);
```

## Features

### 🚀 Easy to Use
Laracord follows Laravel conventions, so if you're familiar with Laravel, you'll feel right at home.

### 🔒 Type Safe
All methods are properly typed with PHP 8+ features, providing excellent IDE support and catch errors at compile time.

### ⚡ High Performance
Built with performance in mind, including automatic retry logic and intelligent rate limiting.

### 🔧 Extensible
Custom middleware support allows you to add your own logic to requests and responses.

### 📚 Well Documented
Comprehensive documentation with examples for every API method.

## Requirements

- PHP 8.1 or higher
- Laravel 10 or higher
- Discord Bot Token

## Next Steps

1. [Install Laracord](./installation/installation.md)
2. [Configure your Discord bot](./installation/configuration.md)
3. [Learn about authentication](./usage/authentication.md)
4. [Explore the API reference](../api.md)

## Support

- **GitHub Issues**: [Report bugs or request features](https://github.com/kyzegs/laracord/issues)
- **Discord**: Join our community server (coming soon)
- **Documentation**: This site contains comprehensive documentation

## Contributing

We welcome contributions! Please see our [contributing guide](https://github.com/kyzegs/laracord/blob/main/CONTRIBUTING.md) for details. 
