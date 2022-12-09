<?php

namespace Webbingbrasil\FilamentTwoFactor;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Str;
use Laragear\TwoFactor\Facades\Auth2FA;

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
