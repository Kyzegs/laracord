<?php

namespace Kyzegs\Laracord\Messages;

class DiscordEmbed
{
    /**
     * Title of embed.
     */
    public ?string $title = null;

    /**
     * Description of embed.
     */
    public ?string $description = null;

    /**
     * URL of embed.
     */
    public ?string $url = null;

    /**
     * Timestamp of embed content.
     */
    public ?int $timestamp = null;

    /**
     * Color code of the embed.
     */
    public ?int $color = null;

    /**
     * Footer information.
     */
    public ?array $footer = null;

    /**
     * Image information.
     */
    public ?array $image = null;

    /**
     * Thumbnail information.
     */
    public ?array $thumbnail = null;

    /**
     * Video information.
     */
    public ?array $video = null;

    /**
     * Provider information.
     */
    public ?array $provider = null;

    /**
     * Author information.
     */
    public ?array $author = null;

    /**
     * Fields information.
     */
    public array $fields = [];

    public function title(?string $title): DiscordEmbed
    {
        $this->title = $title;

        return $this;
    }

    public function type(?string $type): DiscordEmbed
    {
        $this->type = $type;

        return $this;
    }

    public function description(?string $description): DiscordEmbed
    {
        $this->description = $description;

        return $this;
    }

    public function url(?string $url): DiscordEmbed
    {
        $this->url = $url;

        return $this;
    }

    public function timestamp(?int $timestamp): DiscordEmbed
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function color(?int $color): DiscordEmbed
    {
        $this->color = $color;

        return $this;
    }

    public function footer(?array $footer): DiscordEmbed
    {
        $this->footer = $footer;

        return $this;
    }

    public function image(?array $image): DiscordEmbed
    {
        $this->image = $image;

        return $this;
    }

    public function thumbnail(?array $thumbnail): DiscordEmbed
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function video(?array $video): DiscordEmbed
    {
        $this->video = $video;

        return $this;
    }

    public function provider(?array $provider): DiscordEmbed
    {
        $this->provider = $provider;

        return $this;
    }

    public function author(?array $author): DiscordEmbed
    {
        $this->author = $author;

        return $this;
    }

    public function fields(array $fields): DiscordEmbed
    {
        $this->fields = $fields;

        return $this;
    }

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
