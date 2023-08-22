<?php

declare(strict_types=1);

namespace TFSThiagoBR98\FilamentTwoFactor;

use Illuminate\Contracts\Cache\Repository;

class FilamentTwoFactor
{
    /**
     * The cache repository implementation.
     *
     * @var \Illuminate\Contracts\Cache\Repository|null
     */
    protected ?Repository $cache = null;

    /**
     * Create a new two factor authentication provider instance.
     *
     * @param  \Illuminate\Contracts\Cache\Repository|null  $cache
     */
    public function __construct(Repository $cache = null)
    {
        $this->cache = $cache;
    }

    /**
     * Check if a model has totp enabled
     *
     * @param mixed $user
     * @return boolean
     */
    public function hasTwoFactorEnabled(mixed $user): bool
    {
        return optional($user)->hasTwoFactorEnabled();
    }
}
