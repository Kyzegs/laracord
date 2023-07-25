<?php

namespace Kyzegs\Laracord\Messages;

class DiscordMessage
{
    /**
     * Send message in direct messages.
     */
    public bool $private = false;

    /**
     * Message contents (up to 2000 characters).
     */
    public ?string $content = null;

    /**
     * Embedded rich content (up to 6000 characters).
     *
     * @var array<DiscordEmbed>
     */
    public array $embeds = [];

    /**
     * Components to include with the message.
     */
    public array $components = [];

    /**
     * IDs of up to 3 stickers in the server to send in the message.
     */
    public array $stickerIds = [];

    /**
     * Attachment objects with filename and description.
     */
    public array $files = [];

    /**
     * Attachment objects with filename and description.
     */
    public array $attachments = [];

    public function private(bool $private = true): DiscordMessage
    {
        $this->private = $private;

        return $this;
    }

    public function content(?string $content): DiscordMessage
    {
        $this->content = $content;

        return $this;
    }

    public function embed(DiscordEmbed $embed): DiscordMessage
    {
        $this->embeds[] = $embed;

        return $this;
    }

    /**
     * @param  array<DiscordEmbed>  $embeds
     */
    public function embeds(array $embeds): DiscordMessage
    {
        $this->embeds = $embeds;

        return $this;
    }

    public function toArray(): array
    {
        // TODO: Support the whole payload
        return [
            'content' => $this->content,
            'embeds' => array_map(fn (DiscordEmbed $embed) => $embed->toArray(), $this->embeds),
        ];
    }
}
