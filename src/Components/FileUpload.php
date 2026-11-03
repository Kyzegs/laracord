<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Components;

use Kyzegs\Laracord\Components\Concerns\BuildsPayload;
use Kyzegs\Laracord\Components\Concerns\HasId;
use Kyzegs\Laracord\Components\Enums\ComponentType;

final class FileUpload implements Component
{
    use BuildsPayload;
    use HasId;

    private ?int $minValues = null;

    private ?int $maxValues = null;

    private ?bool $required = null;

    /** @var list<string>|null */
    private ?array $fileTypes = null;

    private function __construct(private readonly string $customId)
    {
        self::assertCustomId($customId, 'File upload');
    }

    public static function make(string $customId): self
    {
        return new self($customId);
    }

    public function minValues(int $minValues): self
    {
        if ($minValues < 0 || $minValues > 10) {
            throw new \InvalidArgumentException('File upload min_values must be between 0 and 10.');
        }

        $this->minValues = $minValues;

        return $this;
    }

    public function maxValues(int $maxValues): self
    {
        if ($maxValues < 1 || $maxValues > 10) {
            throw new \InvalidArgumentException('File upload max_values must be between 1 and 10.');
        }

        $this->maxValues = $maxValues;

        return $this;
    }

    public function required(bool $required = true): self
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Limit uploads by extension. Discord does not inspect file contents, so validate them yourself.
     *
     * @param  list<string>  $fileTypes  `image`, `video`, `audio`, or dot-prefixed extensions such as `.pdf`
     */
    public function fileTypes(array $fileTypes): self
    {
        if (count($fileTypes) > 10) {
            throw new \InvalidArgumentException('File upload file_types supports at most 10 entries.');
        }

        foreach ($fileTypes as $fileType) {
            if (! in_array($fileType, ['image', 'video', 'audio'], true) && preg_match('/^\.[^.\s]+$/D', $fileType) !== 1) {
                throw new \InvalidArgumentException(sprintf('File upload file type "%s" must be image, video, audio, or a dot-prefixed extension.', $fileType));
            }
        }

        $this->fileTypes = $fileTypes;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        if ($this->minValues !== null && $this->maxValues !== null && $this->minValues > $this->maxValues) {
            throw new \InvalidArgumentException('File upload min_values cannot exceed max_values.');
        }

        if ($this->minValues === 0 && $this->required !== false) {
            throw new \InvalidArgumentException('File upload min_values may be 0 only when required is false.');
        }

        return $this->withoutNulls([
            'type' => ComponentType::FILE_UPLOAD->value,
            'id' => $this->id,
            'custom_id' => $this->customId,
            'min_values' => $this->minValues,
            'max_values' => $this->maxValues,
            'required' => $this->required,
            'file_types' => $this->fileTypes,
        ]);
    }
}
