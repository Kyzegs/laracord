<?php

declare(strict_types=1);

namespace Kyzegs\Laracord\Socialite;

use Symfony\Component\HttpFoundation\RedirectResponse;

interface ProviderInterface
{
    /**
     * Redirect the user to the authentication page for the provider.
     *
     * @return RedirectResponse
     */
    public function redirect();

    /**
     * Get the User instance for the authenticated user.
     *
     * @return User
     */
    public function user();
}
