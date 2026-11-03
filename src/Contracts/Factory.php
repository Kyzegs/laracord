<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Contracts;

use Kyzegs\Laracord\Interactions\InteractionRouter;
use Kyzegs\Laracord\ValueObjects\OAuthAccessToken;

/**
 * Creates Discord clients bound to a chosen authentication context.
 */
interface Factory
{
    public function bot(): Client;

    public function bearer(string|OAuthAccessToken $token): Client;

    public function withoutAuthentication(): Client;

    public function interactions(): InteractionRouter;
}
