<?php

namespace TFSThiagoBR98\FilamentTwoFactor;

use Filament\Contracts\Plugin;
use Filament\FilamentManager;
use Filament\Panel;
use TFSThiagoBR98\FilamentTwoFactor\Pages\TwoFactor;

class FilamentTwoFactorPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return FilamentTwoFactorProvider::$name;
    }

    public function register(Panel $panel): void
    {
        if (config('filament-2fa.enable_two_factor_page')) {
            $panel->pages([TwoFactor::class]);
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function get(): Plugin | FilamentManager
    {
        return filament(app(static::class)->getId());
    }
}
