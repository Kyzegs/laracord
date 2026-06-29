<?php

declare(strict_types=1);

namespace Kyzegs\Laracord;

use Illuminate\Contracts\Config\Repository;
use Kyzegs\Laracord\Contracts\Factory;
use Kyzegs\Laracord\Interactions\InteractionRouter;
use Kyzegs\Laracord\ValueObjects\Authentication;
use Kyzegs\Laracord\ValueObjects\OAuthAccessToken;

final readonly class LaracordManager implements Factory
{
    public function __construct(
        private ClientFactory $clientFactory,
        private Repository $repository,
        private InteractionRouter $interactionRouter,
    ) {}

    public function bot(): DiscordClient
    {
        $token = (string) $this->repository->get('laracord.bot_token', '');

        return $this->clientFactory->make(Authentication::bot($token));
    }

    public function bearer(string|OAuthAccessToken $token): DiscordClient
    {
        return $this->clientFactory->make(Authentication::bearer($token));
    }

    public function withoutAuthentication(): DiscordClient
    {
        return $this->clientFactory->make(Authentication::none());
    }

    public function interactions(): InteractionRouter
    {
        return $this->interactionRouter;
    }
}
