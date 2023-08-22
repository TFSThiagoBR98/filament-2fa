<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::domain(config('filament.domain'))
    ->middleware(config('filament.middleware.base'))
    ->prefix(config('filament.path'))
    ->group(function () {
        Route::get('/two-factor-challenge', config('filament-2fa.two_factor_challenge_component_path'))
            ->name('filament-2fa.login');
    });
