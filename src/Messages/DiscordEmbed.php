<?php

namespace Kyzegs\Laracord\Messages;

class DiscordEmbed
{
    /**
     * Title of embed.
     *
     * @var string|null
     */
    public ?string $title = null;

    /**
     * Description of embed.
     *
     * @var string|null
     */
    public ?string $description = null;

    /**
     * URL of embed.
     *
     * @var string|null
     */
    public ?string $url = null;

    /**
     * Timestamp of embed content.
     *
     * @var int|null
     */
    public ?int $timestamp = null;

    /**
     * Color code of the embed.
     *
     * @var int|null
     */
    public ?int $color = null;

    /**
     * Footer information.
     *
     * @var array|null
     */
    public ?array $footer = null;

    /**
     * Image information.
     *
     * @var array|null
     */
    public ?array $image = null;

    /**
     * Thumbnail information.
     *
     * @var array|null
     */
    public ?array $thumbnail = null;

    /**
     * Video information.
     *
     * @var array|null
     */
    public ?array $video = null;

    /**
     * Provider information.
     *
     * @var array|null
     */
    public ?array $provider = null;

    /**
     * Author information.
     *
     * @var array|null
     */
    public ?array $author = null;

    /**
     * Fields information.
     *
     * @var array
     */
    public array $fields = [];

    /**
     * @param string|null $title
     * @return DiscordEmbed
     */
    public function title(?string $title): DiscordEmbed
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string|null $type
     * @return DiscordEmbed
     */
    public function type(?string $type): DiscordEmbed
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param string|null $description
     * @return DiscordEmbed
     */
    public function description(?string $description): DiscordEmbed
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param string|null $url
     * @return DiscordEmbed
     */
    public function url(?string $url): DiscordEmbed
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @param int|null $timestamp
     * @return DiscordEmbed
     */
    public function timestamp(?int $timestamp): DiscordEmbed
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @param int|null $color
     * @return DiscordEmbed
     */
    public function color(?int $color): DiscordEmbed
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @param array|null $footer
     * @return DiscordEmbed
     */
    public function footer(?array $footer): DiscordEmbed
    {
        $this->footer = $footer;

        return $this;
    }

    /**
     * @param array|null $image
     * @return DiscordEmbed
     */
    public function image(?array $image): DiscordEmbed
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @param array|null $thumbnail
     * @return DiscordEmbed
     */
    public function thumbnail(?array $thumbnail): DiscordEmbed
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * @param array|null $video
     * @return DiscordEmbed
     */
    public function video(?array $video): DiscordEmbed
    {
        $this->video = $video;

        return $this;
    }

    /**
     * @param array|null $provider
     * @return DiscordEmbed
     */
    public function provider(?array $provider): DiscordEmbed
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @param array|null $author
     * @return DiscordEmbed
     */
    public function author(?array $author): DiscordEmbed
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @param array $fields
     * @return DiscordEmbed
     */
    public function fields(array $fields): DiscordEmbed
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'type' => 'rich',
            'description' => $this->description,
            'url' => $this->url,
            'timestamp' => $this->timestamp,
            'color' => $this->color,
            'footer' => $this->footer,
            'image' => $this->image,
            'thumbnail' => $this->thumbnail,
            'video' => $this->video,
            'provider' => $this->provider,
            'author' => $this->author,
            'fields' => $this->fields,
        ];
    }
}
