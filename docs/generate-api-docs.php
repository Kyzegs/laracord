<?php

require __DIR__ . '/../vendor/autoload.php';

use ReflectionClass;
use ReflectionMethod;

class ApiDocGenerator
{
    private string $facadePath;
    private string $outputDir;
    private array $methods = [];

    public function __construct(string $facadePath, string $outputDir)
    {
        $this->facadePath = $facadePath;
        $this->outputDir = $outputDir;
    }

    public function generate(): void
    {
        $this->parseFacade();
        $this->generateApiPages();
        $this->generateIndexPage();
    }

    private function parseFacade(): void
    {
        $facadeContent = file_get_contents($this->facadePath);

        // Extract @method annotations
        preg_match_all('/@method static (\w+) (\w+)\(([^)]*)\)/', $facadeContent, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $returnType = $match[1];
            $methodName = $match[2];
            $parameters = $this->parseParameters($match[3]);

            $this->methods[] = [
                'name' => $methodName,
                'returnType' => $returnType,
                'parameters' => $parameters,
                'category' => $this->categorizeMethod($methodName),
                'description' => $this->generateDescription($methodName),
                'example' => $this->generateExample($methodName, $parameters)
            ];
        }
    }

    private function parseParameters(string $paramString): array
    {
        if (empty(trim($paramString))) {
            return [];
        }

        $params = [];
        $paramParts = explode(',', $paramString);

        foreach ($paramParts as $part) {
            $part = trim($part);

            // Handle optional parameters with default values
            if (strpos($part, '=') !== false) {
                preg_match('/(\w+)\s+(\w+)\s*=\s*(.+)/', $part, $matches);
                if (count($matches) >= 3) {
                    $params[] = [
                        'type' => $matches[1],
                        'name' => $matches[2],
                        'default' => trim($matches[3]),
                        'required' => false
                    ];
                }
            } else {
                // Handle required parameters
                preg_match('/(\w+)\s+(\w+)/', $part, $matches);
                if (count($matches) >= 3) {
                    $params[] = [
                        'type' => $matches[1],
                        'name' => $matches[2],
                        'default' => null,
                        'required' => true
                    ];
                }
            }
        }

        return $params;
    }

    private function categorizeMethod(string $methodName): string
    {
        if (strpos($methodName, 'ApplicationCommand') !== false) {
            return 'application-commands';
        }
        if (strpos($methodName, 'Channel') !== false || strpos($methodName, 'Message') !== false) {
            return 'channels';
        }
        if (strpos($methodName, 'Guild') !== false) {
            return 'guilds';
        }
        if (strpos($methodName, 'User') !== false) {
            return 'users';
        }
        if (strpos($methodName, 'Webhook') !== false) {
            return 'webhooks';
        }
        return 'general';
    }

    private function generateDescription(string $methodName): string
    {
        // Convert camelCase to readable description
        $description = preg_replace('/([A-Z])/', ' $1', $methodName);
        $description = ucfirst(strtolower(trim($description)));

        // Add specific descriptions based on method name
        $descriptions = [
            'getGlobalApplicationCommands' => 'Retrieve all global application commands for an application.',
            'createGlobalApplicationCommand' => 'Create a new global application command.',
            'getChannel' => 'Get a channel by its ID.',
            'createMessage' => 'Create a new message in a channel.',
            'getGuild' => 'Get a guild by its ID.',
            'getCurrentUser' => 'Get the current user information.',
            'createWebhook' => 'Create a new webhook for a channel.',
        ];

        return $descriptions[$methodName] ?? $description;
    }

    private function generateExample(string $methodName, array $parameters): string
    {
        $example = "```php\n";
        $example .= "use Kyzegs\\Laracord\\Facades\\Laracord;\n\n";

        $paramList = [];
        foreach ($parameters as $param) {
            $paramList[] = '$' . $param['name'];
        }

        $example .= "$result = Laracord::{$methodName}(" . implode(', ', $paramList) . ");\n";
        $example .= "```";

        return $example;
    }

    private function generateApiPages(): void
    {
        $categories = [
            'application-commands' => 'Application Commands',
            'channels' => 'Channels & Messages',
            'guilds' => 'Guilds',
            'users' => 'Users',
            'webhooks' => 'Webhooks',
            'general' => 'General'
        ];

        foreach ($categories as $category => $title) {
            $categoryMethods = array_filter($this->methods, fn($m) => $m['category'] === $category);

            if (!empty($categoryMethods)) {
                $this->generateCategoryPage($category, $title, $categoryMethods);
            }
        }
    }

    private function generateCategoryPage(string $category, string $title, array $methods): void
    {
        $content = "# {$title}\n\n";
        $content .= "This section contains all methods related to {$title}.\n\n";

        foreach ($methods as $method) {
            $content .= $this->generateMethodDocumentation($method);
        }

        $outputPath = $this->outputDir . "/docs/api/{$category}.md";
        $this->ensureDirectoryExists(dirname($outputPath));
        file_put_contents($outputPath, $content);
    }

    private function generateMethodDocumentation(array $method): string
    {
        $content = "## {$method['name']}\n\n";
        $content .= "{$method['description']}\n\n";

        // Method signature
        $signature = "```php\n";
        $signature .= "public static {$method['returnType']} {$method['name']}(";

        $params = [];
        foreach ($method['parameters'] as $param) {
            $paramStr = "{$param['type']} \${$param['name']}";
            if (!$param['required']) {
                $paramStr .= " = {$param['default']}";
            }
            $params[] = $paramStr;
        }

        $signature .= implode(', ', $params);
        $signature .= ")\n```\n\n";

        $content .= $signature;

        // Parameters table
        if (!empty($method['parameters'])) {
            $content .= "### Parameters\n\n";
            $content .= "| Parameter | Type | Required | Default | Description |\n";
            $content .= "|-----------|------|----------|---------|-------------|\n";

            foreach ($method['parameters'] as $param) {
                $required = $param['required'] ? 'Yes' : 'No';
                $default = $param['default'] ?? '-';
                $content .= "| `{$param['name']}` | `{$param['type']}` | {$required} | {$default} | - |\n";
            }
            $content .= "\n";
        }

        // Return type
        $content .= "### Returns\n\n";
        $content .= "Returns `{$method['returnType']}`\n\n";

        // Example
        $content .= "### Example\n\n";
        $content .= $method['example'] . "\n\n";

        $content .= "---\n\n";

        return $content;
    }

    private function generateIndexPage(): void
    {
        $content = "# API Reference\n\n";
        $content .= "Welcome to the Laracord API reference. This documentation covers all available methods in the Laracord Facade.\n\n";

        $content .= "## Quick Start\n\n";
        $content .= "```php\n";
        $content .= "use Kyzegs\\Laracord\\Facades\\Laracord;\n\n";
        $content .= "// Get a channel\n";
        $content .= "$channel = Laracord::getChannel(123456789);\n\n";
        $content .= "// Create a message\n";
        $content .= "$message = Laracord::createMessage(123456789, [\n";
        $content .= "    'content' => 'Hello, Discord!'\n";
        $content .= "]);\n";
        $content .= "```\n\n";

        $content .= "## Method Categories\n\n";

        $categories = [
            'application-commands' => 'Application Commands',
            'channels' => 'Channels & Messages',
            'guilds' => 'Guilds',
            'users' => 'Users',
            'webhooks' => 'Webhooks'
        ];

        foreach ($categories as $category => $title) {
            $categoryMethods = array_filter($this->methods, fn($m) => $m['category'] === $category);
            if (!empty($categoryMethods)) {
                $content .= "- [{$title}](./api/{$category}.md) (" . count($categoryMethods) . " methods)\n";
            }
        }

        $content .= "\n## All Methods\n\n";

        foreach ($this->methods as $method) {
            $content .= "- [`{$method['name']}`](./api/{$method['category']}.md#{$method['name']}) - {$method['description']}\n";
        }

        $outputPath = $this->outputDir . "/docs/api.md";
        $this->ensureDirectoryExists(dirname($outputPath));
        file_put_contents($outputPath, $content);
    }

    private function ensureDirectoryExists(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}

// Usage
$generator = new ApiDocGenerator(
    __DIR__ . '/../src/Facades/Laracord.php',
    __DIR__
);

$generator->generate();

echo "API documentation generated successfully!\n";
