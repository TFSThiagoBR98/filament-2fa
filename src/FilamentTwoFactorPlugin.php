<?php

declare (strict_types=1);

namespace TFSThiagoBR98\FilamentTwoFactor;

use Filament\Contracts\Plugin;
use Filament\FilamentManager;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use TFSThiagoBR98\FilamentTwoFactor\Pages\TwoFactor;
use Illuminate\Support\Facades\Route;

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
            $panel = $panel->pages([TwoFactor::class]);
        }

        $panel = $panel->routes(function () {
            Route::get('/two-factor-challenge', config('filament-2fa.two_factor_challenge_component_path'))
                ->name('filament-2fa.login');
        });
    }

    public function boot(Panel $panel): void
    {
        if (config('filament-2fa.enable_two_factor_page') && config('filament-2fa.show_two_factor_page_in_user_menu')) {
            $panel = $panel->userMenuItems([
                MenuItem::make()
                    ->label(__('filament-2fa::two-factor.navigation_label'))
                    ->url(TwoFactor::getUrl())
                    ->icon('heroicon-s-lock-closed'),
            ]);
        }
    }

    public static function get(): Plugin | FilamentManager
    {
        return filament(app(static::class)->getId());
    }
}
