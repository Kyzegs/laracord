<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Payloads;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/** @implements Arrayable<string, mixed> */
final class DiscordMessage implements Arrayable, JsonSerializable
{
    /** @var array<string, mixed> */
    private array $data = ['allowed_mentions' => ['parse' => []]];

    /** @var list<array<string, mixed>> */
    private array $files = [];

    public function content(string $content): self
    {
        if (mb_strlen($content) > 2000) {
            throw new \InvalidArgumentException('Discord message content cannot exceed 2000 characters.');
        }

        $this->data['content'] = $content;

        return $this;
    }

    public function username(string $username): self
    {
        $this->data['username'] = $username;

        return $this;
    }

    public function avatar(string $url): self
    {
        $this->data['avatar_url'] = $url;

        return $this;
    }

    public function tts(bool $enabled = true): self
    {
        $this->data['tts'] = $enabled;

        return $this;
    }

    public function flags(int $flags): self
    {
        $this->data['flags'] = $flags;

        return $this;
    }

    /** @param list<array<string, mixed>> $components */
    public function components(array $components): self
    {
        $this->data['components'] = $components;

        return $this;
    }

    /** @param array<string, mixed>|Arrayable<string, mixed> $poll */
    public function poll(array|Arrayable $poll): self
    {
        $this->data['poll'] = $poll instanceof Arrayable ? $poll->toArray() : $poll;

        return $this;
    }

    /** @param array<string, mixed> $allowedMentions */
    public function allowedMentions(array $allowedMentions): self
    {
        $this->data['allowed_mentions'] = $allowedMentions;

        return $this;
    }

    /** @param array<string, mixed>|DiscordEmbed $embed */
    public function embed(DiscordEmbed|array $embed): self
    {
        $this->data['embeds'] ??= [];
        if (count($this->data['embeds']) >= 10) {
            throw new \InvalidArgumentException('Discord messages support at most 10 embeds.');
        }

        $this->data['embeds'][] = $embed instanceof DiscordEmbed ? $embed->toArray() : $embed;

        return $this;
    }

    public function file(mixed $contents, string $filename, ?string $contentType = null): self
    {
        $this->files[] = array_filter(['contents' => $contents, 'filename' => $filename, 'contentType' => $contentType]);

        return $this;
    }

    /** @return list<array<string, mixed>> */
    public function files(): array
    {
        return array_map(static fn (array $file): array => [
            'contents' => $file['contents'],
            'filename' => $file['filename'],
            'content_type' => $file['contentType'] ?? null,
        ], $this->files);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->data;
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->data;
    }
}
