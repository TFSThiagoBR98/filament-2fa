<?php

namespace TFSThiagoBR98\FilamentTwoFactor\Pages;

use Filament\Pages\Page;

class TwoFactor extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static string $view = 'filament-2fa::filament.pages.two-factor';

    public static function getNavigationLabel(): string
    {
        return __('filament-2fa::two-factor.navigation_label');
    }

    public function getTitle(): string
    {
        return __('filament-2fa::two-factor.title');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('filament-2fa.show_two_factor_page_in_navbar');
    }
}
