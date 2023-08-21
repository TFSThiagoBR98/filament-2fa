<?php

namespace TFSThiagoBR98\FilamentTwoFactor;

use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Filament\PluginServiceProvider;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use TFSThiagoBR98\FilamentTwoFactor\Http\Livewire\Auth;
use TFSThiagoBR98\FilamentTwoFactor\Http\Livewire\TwoFactorAuthenticationForm;
use TFSThiagoBR98\FilamentTwoFactor\Pages\TwoFactor;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentTwoFactorProvider extends PackageServiceProvider
{
    public static string $name = 'filament-2fa';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(name: static::$name)
            ->hasRoute('web')
            ->hasViews();

        Livewire::component(Auth\Login::getName(), Auth\Login::class);
        Livewire::component(Auth\TwoFactorChallenge::getName(), Auth\TwoFactorChallenge::class);
        Livewire::component('filament-two-factor-form', TwoFactorAuthenticationForm::class);
    }

    public function packageBooted(): void
    {
        if (config('filament-2fa.enable_two_factor_page')
            && config('filament-2fa.show_two_factor_page_in_user_menu')) {
            Filament::serving(function () {
                Filament::registerUserMenuItems([
                    MenuItem::make()
                        ->label(__('filament-2fa::two-factor.navigation_label'))
                        ->url(TwoFactor::getUrl())
                        ->icon('heroicon-s-lock-closed'),
                ]);
            });
        }
    }

    protected function getPages(): array
    {
        return config('filament-2fa.enable_two_factor_page')
            ? [TwoFactor::class]
            : [];
    }
}
