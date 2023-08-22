<?php

declare(strict_types=1);

namespace TFSThiagoBR98\FilamentTwoFactor;

use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use TFSThiagoBR98\FilamentTwoFactor\Http\Livewire\Auth;
use TFSThiagoBR98\FilamentTwoFactor\Http\Livewire\TwoFactorAuthenticationForm;

class FilamentTwoFactorProvider extends PackageServiceProvider
{
    public static string $name = 'filament-2fa';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(name: static::$name)
            ->hasTranslations()
            ->hasViews();
    }

    public function packageBooted(): void
    {
        Livewire::component('filament-two-factor-page-login', Auth\Login::class);
        Livewire::component('filament-two-factor-challenge', Auth\TwoFactorChallenge::class);
        Livewire::component('filament-two-factor-form', TwoFactorAuthenticationForm::class);
    }
}
