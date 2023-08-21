<?php

namespace TFSThiagoBR98\FilamentTwoFactor;

use Illuminate\Contracts\Cache\Repository;

class FilamentTwoFactor
{
    /**
     * The cache repository implementation.
     *
     * @var \Illuminate\Contracts\Cache\Repository|null
     */
    protected $cache;

    /**
     * Create a new two factor authentication provider instance.
     *
     * @param  \Illuminate\Contracts\Cache\Repository|null  $cache
     * @return void
     */
    public function __construct(Repository $cache = null)
    {
        $this->cache = $cache;
    }

    public function hasTwoFactorEnabled($user)
    {
        return optional($user)->hasTwoFactorEnabled();
    }
}
