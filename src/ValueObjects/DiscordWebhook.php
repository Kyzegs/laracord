<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\ValueObjects;

use Stringable;

final readonly class DiscordWebhook implements Stringable
{
    public function __construct(public string $id, private string $token)
    {
        new Snowflake($id);
        if ($token === '') {
            throw new \InvalidArgumentException('Discord webhook token cannot be empty.');
        }
    }

    public static function fromUrl(string $url): self
    {
        $parts = parse_url($url);
        if (($parts['scheme'] ?? null) !== 'https' || ! in_array(strtolower((string) ($parts['host'] ?? '')), ['discord.com', 'discordapp.com'], true)) {
            throw new \InvalidArgumentException('Discord webhook URL must use HTTPS on discord.com.');
        }

        if (preg_match('#/api(?:/v\d+)?/webhooks/(\d+)/([^/?]+)#', (string) ($parts['path'] ?? ''), $matches) !== 1) {
            throw new \InvalidArgumentException('Invalid Discord webhook URL.');
        }

        return new self($matches[1], $matches[2]);
    }

    public function token(): string
    {
        return $this->token;
    }

    public function __toString(): string
    {
        return sprintf('https://discord.com/api/webhooks/%s/[REDACTED]', $this->id);
    }
}
