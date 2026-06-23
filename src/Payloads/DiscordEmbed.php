<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Payloads;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/** @implements Arrayable<string, mixed> */
final class DiscordEmbed implements Arrayable, JsonSerializable
{
    /** @var list<array{name:string,value:string,inline:bool}> */
    private array $fields = [];

    /** @var array<string, mixed> */
    private array $data = [];

    public function title(string $title, ?string $url = null): self
    {
        if (mb_strlen($title) > 256) {
            throw new \InvalidArgumentException('Discord embed title cannot exceed 256 characters.');
        }

        $this->data['title'] = $title;
        if ($url !== null) {
            $this->data['url'] = $url;
        }

        return $this;
    }

    public function description(string $description): self
    {
        if (mb_strlen($description) > 4096) {
            throw new \InvalidArgumentException('Discord embed description cannot exceed 4096 characters.');
        }

        $this->data['description'] = $description;

        return $this;
    }

    public function color(int $color): self
    {
        $this->data['color'] = $color;

        return $this;
    }

    public function timestamp(\DateTimeInterface $timestamp): self
    {
        $this->data['timestamp'] = $timestamp->format(DATE_ATOM);

        return $this;
    }

    public function image(string $url): self
    {
        $this->data['image'] = ['url' => $url];

        return $this;
    }

    public function thumbnail(string $url): self
    {
        $this->data['thumbnail'] = ['url' => $url];

        return $this;
    }

    public function author(string $name, ?string $url = null, ?string $iconUrl = null): self
    {
        $this->data['author'] = array_filter(['name' => $name, 'url' => $url, 'icon_url' => $iconUrl], fn (?string $value): bool => $value !== null);

        return $this;
    }

    public function footer(string $text, ?string $iconUrl = null): self
    {
        $this->data['footer'] = array_filter(['text' => $text, 'icon_url' => $iconUrl], fn (?string $value): bool => $value !== null);

        return $this;
    }

    public function field(string $name, string $value, bool $inline = false): self
    {
        if (count($this->fields) >= 25) {
            throw new \InvalidArgumentException('Discord embeds support at most 25 fields.');
        }

        $this->fields[] = ['name' => $name, 'value' => $value, 'inline' => $inline];

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->fields === [] ? $this->data : [...$this->data, 'fields' => $this->fields];
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
