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
    protected ?Repository $cache;

    /**
     * Create a new two factor authentication provider instance.
     *
     * @param  \Illuminate\Contracts\Cache\Repository|null  $cache
     */
    public function __construct(Repository $cache = null)
    {
        $this->cache = $cache;
    }

    public function hasTwoFactorEnabled(mixed $user): mixed
    {
        return optional($user)->hasTwoFactorEnabled();
    }
}
