<?php

namespace Kyzegs\Laracord;

use Illuminate\Support\Arr;

class Route
{
    public const BASE_URL = 'https://discord.com/api/v10';

    private ?int $channelId = null;

    private ?int $guildId = null;

    private ?int $webhookId = null;

    private ?string $webhookToken = null;

    public function __construct(
        private string $method,
        private string $path,
        private array $parameters = [],
        private ?string $metadata = null
    ) {
        $this->channelId = Arr::get($parameters, 'channel_id');
        $this->guildId = Arr::get($parameters, 'guild_id');
        $this->webhookId = Arr::get($parameters, 'webhook_id');
        $this->webhookToken = Arr::get($parameters, 'webhook_token');
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    private function formatPath(): string
    {
        $path = $this->path;

        foreach ($this->parameters as $key => $value) {
            $path = str_replace(sprintf('{%s}', $key), $value, $path);
        }

        return $path;
    }

    public function getUrl(): string
    {
        return sprintf('%s%s', self::BASE_URL, $this->formatPath());
    }

    public function getKey(): string
    {
        return $this->metadata
            ? sprintf('%s %s:%s', $this->method, $this->path, $this->metadata)
            : sprintf('%s %s', $this->method, $this->path);
    }

    public function getMajorParameters(): string
    {
        return implode('+', array_filter([$this->channelId, $this->guildId, $this->webhookId, $this->webhookToken]));
    }

    public function getBucket(): Bucket
    {
        return new Bucket($this);
    }

    public function getBucketHash(): BucketHash
    {
        return new BucketHash($this);
    }
}
