<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Payloads;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Kyzegs\Laracord\Components\Concerns\ValidatesIdentifiers;
use Kyzegs\Laracord\Components\Enums\ComponentType;
use Kyzegs\Laracord\Constants\MessageFlags;

/** @implements Arrayable<string, mixed> */
final class DiscordMessage implements Arrayable, JsonSerializable
{
    use ValidatesIdentifiers;

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
        $this->data['flags'] = $this->componentsV2 ? $flags | MessageFlags::IS_COMPONENTS_V2 : $flags;

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
        $this->data['flags'] = ($this->data['flags'] ?? 0) | MessageFlags::IS_COMPONENTS_V2;

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

    /** Reply to a message in the same channel. */
    public function reply(string $messageId, bool $failIfNotExists = true): self
    {
        $this->data['message_reference'] = ['type' => 0, 'message_id' => $messageId, 'fail_if_not_exists' => $failIfNotExists];

        return $this;
    }

    /** Forward a message, which needs the channel it came from. */
    public function forward(string $messageId, string $channelId): self
    {
        $this->data['message_reference'] = ['type' => 1, 'message_id' => $messageId, 'channel_id' => $channelId];

        return $this;
    }

    /** @param list<string> $stickerIds */
    public function stickers(array $stickerIds): self
    {
        if (count($stickerIds) > 3) {
            throw new \InvalidArgumentException('Discord messages support at most 3 stickers.');
        }

        $this->data['sticker_ids'] = $stickerIds;

        return $this;
    }

    /**
     * With `$enforce`, Discord returns the original message when the same nonce
     * was used recently, which makes a failed create safe to send again. Only
     * Create Message takes a nonce, not webhooks or interaction follow-ups.
     */
    public function nonce(string|int $nonce, bool $enforce = true): self
    {
        $this->data['nonce'] = $nonce;
        $this->data['enforce_nonce'] = $enforce;

        return $this;
    }

    /** Name of the thread to create when sending through a webhook to a forum or media channel. */
    public function threadName(string $name): self
    {
        $this->data['thread_name'] = $name;

        return $this;
    }

    /** @param list<string> $tagIds Tags for the thread created by a webhook in a forum or media channel. */
    public function appliedTags(array $tagIds): self
    {
        $this->data['applied_tags'] = $tagIds;

        return $this;
    }

    /** @param array<string, mixed> $theme */
    public function sharedClientTheme(array $theme): self
    {
        $this->data['shared_client_theme'] = $theme;

        return $this;
    }

    public function file(mixed $contents, string $filename, ?string $contentType = null, ?string $description = null, bool $spoiler = false): self
    {
        $this->files[] = ['contents' => $contents, 'filename' => $filename, 'contentType' => $contentType, 'description' => $description, 'spoiler' => $spoiler];

        return $this;
    }

    /** @return list<array<string, mixed>> */
    public function files(): array
    {
        return array_map(static fn (array $file): array => [
            'contents' => $file['contents'],
            'filename' => $file['filename'],
            'content_type' => $file['contentType'],
        ], $this->files);
    }

    /**
     * Discord matches each entry to an upload by the `n` in `files[n]`, and an
     * `attachments` array that omits a new file would leave it out of the message.
     *
     * @return list<array<string, mixed>>
     */
    private function attachments(): array
    {
        return array_map(static fn (int $index, array $file): array => array_filter([
            'id' => $index,
            'filename' => $file['filename'],
            'description' => $file['description'],
            'is_spoiler' => $file['spoiler'] ?: null,
        ], static fn (mixed $value): bool => $value !== null), array_keys($this->files), $this->files);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        if ($this->componentsV2) {
            foreach (['content', 'embeds', 'poll', 'sticker_ids', 'shared_client_theme'] as $field) {
                if (array_key_exists($field, $this->data)) {
                    throw new \InvalidArgumentException(sprintf('Discord Components V2 messages cannot contain %s.', $field));
                }
            }

            $components = $this->data['components'] ?? [];
            if ($components === []) {
                throw new \InvalidArgumentException('A Discord Components V2 message requires at least one component.');
            }

            $topLevelTypes = [
                ComponentType::ACTION_ROW->value,
                ComponentType::SECTION->value,
                ComponentType::TEXT_DISPLAY->value,
                ComponentType::MEDIA_GALLERY->value,
                ComponentType::FILE->value,
                ComponentType::SEPARATOR->value,
                ComponentType::CONTAINER->value,
            ];
            foreach ($components as $component) {
                if (! isset($component['type']) || ! in_array($component['type'], $topLevelTypes, true)) {
                    throw new \InvalidArgumentException('Discord Components V2 message contains an unsupported top-level component.');
                }
            }

            if ($this->componentCount($components) > 40) {
                throw new \InvalidArgumentException('Discord Components V2 messages support at most 40 total components.');
            }

            $this->validateUniqueIdentifiers($components, 'message');
        }

        $hasMetadata = array_filter($this->files, static fn (array $file): bool => $file['description'] !== null || $file['spoiler']) !== [];

        return $hasMetadata ? [...$this->data, 'attachments' => $this->attachments()] : $this->data;
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
}
