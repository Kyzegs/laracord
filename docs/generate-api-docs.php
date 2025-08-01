<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TypeParser;
use PHPStan\PhpDocParser\Parser\ConstExprParser;

class ApiDocGenerator
{
    private string $facadePath;
    private string $clientPath;
    private string $outputDir;
    private array $methods = [];
    private array $clientDocstrings = [];
    private PhpDocParser $phpDocParser;

    public function __construct(string $facadePath, string $clientPath, string $outputDir)
    {
        $this->facadePath = $facadePath;
        $this->clientPath = $clientPath;
        $this->outputDir = $outputDir;

        // Initialize PHPStan's PhpDocParser
        $lexer = new Lexer();
        $constExprParser = new ConstExprParser();
        $typeParser = new TypeParser($constExprParser);
        $this->phpDocParser = new PhpDocParser($typeParser, $constExprParser);
    }

    public function generate(): void
    {
        $this->parseClientDocstrings();
        $this->parseFacade();
        $this->generateApiPages();
        $this->generateIndexPage();
    }

    private function parseClientDocstrings(): void
    {
        // Use PHP's Reflection API like facades.php does
        $clientClass = new \ReflectionClass('Kyzegs\Laracord\Client');

        foreach ($clientClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $methodName = $method->getName();
            $docComment = $method->getDocComment();

            if ($docComment) {
                $parsed = $this->parseDocstringBlock($docComment);
                $this->clientDocstrings[$methodName] = $parsed;
            }
        }
    }

    private function parseDocstringBlock(string $docstringBlock): array
    {
        try {
            // Tokenize the docstring using PHPStan's lexer
            $lexer = new Lexer();
            $tokens = $lexer->tokenize($docstringBlock);
            $tokenIterator = new \PHPStan\PhpDocParser\Parser\TokenIterator($tokens);

            // Parse the docstring using PHPStan's parser
            $phpDocNode = $this->phpDocParser->parse($tokenIterator);

            $description = '';
            $paramDescriptions = [];

            // Extract description from the text content
            if ($phpDocNode->children) {
                foreach ($phpDocNode->children as $child) {
                    if ($child instanceof \PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode) {
                        $text = trim($child->text);
                        if (!empty($text) && !str_starts_with($text, '@')) {
                            $description .= $text . ' ';
                        }
                    }
                }
            }

            // Extract @param annotations
            foreach ($phpDocNode->children as $child) {
                if ($child instanceof \PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode) {
                    if ($child->name === '@param') {
                        $paramTag = $child->value;
                        if ($paramTag instanceof \PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode) {
                            $paramName = $paramTag->parameterName;
                            $paramType = $paramTag->type->name ?? 'mixed';
                            $paramDescription = trim($paramTag->description);

                            $paramDescriptions[$paramName] = [
                                'type' => $paramType,
                                'description' => $paramDescription
                            ];
                        }
                    }
                }
            }

            return [
                'description' => trim($description),
                'params' => $paramDescriptions
            ];
        } catch (\Exception $e) {
            // Fallback to simple parsing if PHPStan parser fails
            return $this->fallbackParseDocstring($docstringBlock);
        }
    }

    private function fallbackParseDocstring(string $docstringBlock): array
    {
        $lines = explode("\n", $docstringBlock);
        $description = '';
        $paramDescriptions = [];

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip empty lines and comment markers
            if (empty($line) || $line === '*' || $line === '*/') {
                continue;
            }

            // Remove leading * and spaces
            $line = preg_replace('/^\*\s*/', '', $line);

            // Check for @param annotations
            if (preg_match('/@param\s+(\w+)\s+\$(\w+)\s+(.+)/', $line, $matches)) {
                $paramType = $matches[1];
                $paramName = $matches[2];
                $paramDescription = trim($matches[3]);
                $paramDescriptions[$paramName] = [
                    'type' => $paramType,
                    'description' => $paramDescription
                ];
            } else {
                // This is part of the main description
                $description .= $line . ' ';
            }
        }

        return [
            'description' => trim($description),
            'params' => $paramDescriptions
        ];
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
                'description' => $this->getDescription($methodName),
                'example' => $this->generateExample($methodName, $parameters)
            ];
        }
    }

    private function getDescription(string $methodName): string
    {
        // First try to get the docstring from Client.php
        if (isset($this->clientDocstrings[$methodName])) {
            $parsed = $this->clientDocstrings[$methodName];
            return $parsed['description'];
        }

        // Fallback to generated description
        $description = preg_replace('/([A-Z])/', ' $1', $methodName);
        $description = ucfirst(strtolower(trim($description)));

        return $description;
    }

    private function getParameterDescription(string $methodName, string $paramName): string
    {
        // Try to get from parsed docstrings
        if (isset($this->clientDocstrings[$methodName])) {
            $parsed = $this->clientDocstrings[$methodName];
            if (isset($parsed['params'][$paramName])) {
                return $parsed['params'][$paramName]['description'];
            }
            // Try with $ prefix
            if (isset($parsed['params']['$' . $paramName])) {
                return $parsed['params']['$' . $paramName]['description'];
            }
        }

        return '-';
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
                // Match: type $name = default
                if (preg_match('/(\w+)\s+\$(\w+)\s*=\s*(.+)/', $part, $matches)) {
                    $params[] = [
                        'type' => $matches[1],
                        'name' => $matches[2],
                        'default' => trim($matches[3]),
                        'required' => false
                    ];
                }
            } else {
                // Handle required parameters
                // Match: type $name
                if (preg_match('/(\w+)\s+\$(\w+)/', $part, $matches)) {
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

    private function generateExample(string $methodName, array $parameters): string
    {
        $example = "```php\n";
        $example .= "use Kyzegs\\Laracord\\Facades\\Laracord;\n\n";

        // Create a simple example based on the method name
        $example .= "// Example usage\n";
        $example .= "Laracord::{$methodName}(";

        $paramList = [];
        foreach ($parameters as $param) {
            $paramList[] = '$' . $param['name'];
        }

        $example .= implode(', ', $paramList);
        $example .= ");\n";
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
                $description = $this->getParameterDescription($method['name'], $param['name']);
                $content .= "| `{$param['name']}` | `{$param['type']}` | {$required} | {$default} | {$description} |\n";
            }
            $content .= "\n";
        }

        // Return type
        $content .= "### Returns\n\n";
        $content .= "Returns `{$method['returnType']}`\n\n";

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
        $content .= "\$channel = Laracord::getChannel(123456789);\n\n";
        $content .= "// Create a message\n";
        $content .= "\$message = Laracord::createMessage(123456789, [\n";
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
    __DIR__ . '/../src/Client.php',
    __DIR__
);

$generator->generate();

echo "API documentation generated successfully!\n";
