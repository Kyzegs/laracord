<?php

namespace Kyzegs\Laracord\Messages;

class DiscordMessage
{
    /**
     * Send message in direct messages.
     *
     * @var bool
     */
    public bool $private = false;

    /**
     * Message contents (up to 2000 characters).
     *
     * @var string|null
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
     *
     * @var array
     */
    public array $components = [];

    /**
     * IDs of up to 3 stickers in the server to send in the message.
     *
     * @var array
     */
    public array $stickerIds = [];

    /**
     * Attachment objects with filename and description.
     *
     * @var array
     */
    public array $files = [];

    /**
     * Attachment objects with filename and description.
     *
     * @var array
     */
    public array $attachments = [];

    /**
     * @param  bool  $private
     * @return DiscordMessage
     */
    public function private(bool $private = true): DiscordMessage
    {
        $this->private = $private;

        return $this;
    }

    /**
     * @param  string|null  $content
     * @return DiscordMessage
     */
    public function content(?string $content): DiscordMessage
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @param  DiscordEmbed  $embed
     * @return DiscordMessage
     */
    public function embed(DiscordEmbed $embed): DiscordMessage
    {
        $this->embeds[] = $embed;

        return $this;
    }

    /**
     * @param  array<DiscordEmbed>  $embeds
     * @return DiscordMessage
     */
    public function embeds(array $embeds): DiscordMessage
    {
        $this->embeds = $embeds;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        // TODO: Support the whole payload
        return [
            'content' => $this->content,
            'embeds' => array_map(fn (DiscordEmbed $embed) => $embed->toArray(), $this->embeds),
        ];
    }
}
