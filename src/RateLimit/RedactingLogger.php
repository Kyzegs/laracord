<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\RateLimit;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Stringable;

/**
 * The rate limit middleware logs full request URLs, and Discord's webhook and
 * interaction URLs contain their token.
 */
final class RedactingLogger extends AbstractLogger
{
    public function __construct(private readonly LoggerInterface $logger) {}

    public static function redact(string $text): string
    {
        return (string) preg_replace('#(/(?:webhooks|interactions)/[^/\s?]+/)[^/\s?]+#', '$1[REDACTED]', $text);
    }

    /** @param array<array-key, mixed> $context */
    public function log($level, Stringable|string $message, array $context = []): void
    {
        $this->logger->log($level, self::redact((string) $message), $context);
    }
}
