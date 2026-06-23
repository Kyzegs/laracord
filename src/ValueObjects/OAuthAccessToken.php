<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\ValueObjects;

use DateTimeImmutable;
use JsonSerializable;

final readonly class OAuthAccessToken implements JsonSerializable
{
    /** @param list<string> $scopes */
    public function __construct(
        public string $accessToken,
        public ?string $refreshToken = null,
        public ?DateTimeImmutable $expiresAt = null,
        public array $scopes = [],
    ) {
        if ($accessToken === '') {
            throw new \InvalidArgumentException('Discord access token cannot be empty.');
        }
    }

    /** @return array{access_token:string,refresh_token:?string,expires_at:?string,scopes:list<string>} */
    public function jsonSerialize(): array
    {
        return [
            'access_token' => '[REDACTED]',
            'refresh_token' => $this->refreshToken === null ? null : '[REDACTED]',
            'expires_at' => $this->expiresAt?->format(DATE_ATOM),
            'scopes' => $this->scopes,
        ];
    }
}
