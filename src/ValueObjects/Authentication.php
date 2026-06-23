<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\ValueObjects;

use Kyzegs\Laracord\Enums\AuthenticationType;

final readonly class Authentication
{
    private function __construct(public AuthenticationType $type, private ?string $token) {}

    public static function bot(string $token): self
    {
        return new self(AuthenticationType::Bot, self::validate($token));
    }

    public static function bearer(string|OAuthAccessToken $token): self
    {
        return new self(AuthenticationType::Bearer, self::validate($token instanceof OAuthAccessToken ? $token->accessToken : $token));
    }

    public static function none(): self
    {
        return new self(AuthenticationType::None, null);
    }

    public function header(): ?string
    {
        return $this->token === null ? null : $this->type->value.' '.$this->token;
    }

    public function fingerprint(): string
    {
        return $this->token === null ? 'unauthenticated' : hash('sha256', $this->type->value."\0".$this->token);
    }

    private static function validate(string $token): string
    {
        if (trim($token) === '') {
            throw new \InvalidArgumentException('Discord authentication token cannot be empty.');
        }

        return $token;
    }
}
