<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Payloads;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/** @implements Arrayable<string, mixed> */
final class DiscordMessage implements Arrayable, JsonSerializable
{
    public const IS_COMPONENTS_V2 = 1 << 15;

    /** @var array<string, mixed> */
    private array $data = ['allowed_mentions' => ['parse' => []]];

    /** @var list<array<string, mixed>> */
    private array $files = [];

    private bool $componentsV2 = false;

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
        $this->data['flags'] = $this->componentsV2 ? $flags | self::IS_COMPONENTS_V2 : $flags;

        return $this;
    }

    /** @param list<Arrayable<string, mixed>|array<string, mixed>> $components */
    public function components(array $components): self
    {
        $this->data['components'] = array_map(
            static fn (array|Arrayable $component): array => $component instanceof Arrayable ? $component->toArray() : $component,
            $components,
        );

        return $this;
    }

    /** @param list<Arrayable<string, mixed>|array<string, mixed>> $components */
    public function componentsV2(array $components): self
    {
        $this->componentsV2 = true;
        $this->data['flags'] = ($this->data['flags'] ?? 0) | self::IS_COMPONENTS_V2;

        return $this->components($components);
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
        if ($this->componentsV2) {
            foreach (['content', 'embeds', 'poll', 'sticker_ids'] as $field) {
                if (array_key_exists($field, $this->data)) {
                    throw new \InvalidArgumentException("Discord Components V2 messages cannot contain {$field}.");
                }
            }

            $components = $this->data['components'] ?? [];
            if ($components === []) {
                throw new \InvalidArgumentException('A Discord Components V2 message requires at least one component.');
            }

            $topLevelTypes = [1, 9, 10, 12, 13, 14, 17];
            foreach ($components as $component) {
                if (! isset($component['type']) || ! in_array($component['type'], $topLevelTypes, true)) {
                    throw new \InvalidArgumentException('Discord Components V2 message contains an unsupported top-level component.');
                }
            }

            if ($this->componentCount($components) > 40) {
                throw new \InvalidArgumentException('Discord Components V2 messages support at most 40 total components.');
            }

            $ids = $customIds = [];
            $this->validateUniqueIdentifiers($components, $ids, $customIds);
        }

        return $this->data;
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /** @param array<array-key, mixed> $value */
    private function componentCount(array $value): int
    {
        $count = isset($value['type']) && is_int($value['type']) ? 1 : 0;

        foreach ($value as $child) {
            if (is_array($child)) {
                $count += $this->componentCount($child);
            }
        }

        return $count;
    }

    /**
     * @param  array<array-key, mixed>  $value
     * @param  array<int, true>  $ids
     * @param  array<string, true>  $customIds
     */
    private function validateUniqueIdentifiers(array $value, array &$ids, array &$customIds): void
    {
        if (isset($value['id']) && is_int($value['id']) && $value['id'] !== 0) {
            if (isset($ids[$value['id']])) {
                throw new \InvalidArgumentException('Component ids must be unique within a message.');
            }

            $ids[$value['id']] = true;
        }

        if (isset($value['custom_id']) && is_string($value['custom_id'])) {
            if (isset($customIds[$value['custom_id']])) {
                throw new \InvalidArgumentException('Component custom_ids must be unique within a message.');
            }

            $customIds[$value['custom_id']] = true;
        }

        foreach ($value as $child) {
            if (is_array($child)) {
                $this->validateUniqueIdentifiers($child, $ids, $customIds);
            }
        }
    }
}
