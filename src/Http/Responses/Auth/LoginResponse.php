<?php

declare(strict_types=1);

namespace TFSThiagoBR98\FilamentTwoFactor\Http\Responses\Auth;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector as Redirector;

class LoginResponse implements Responsable
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $painel = Filament::getCurrentPanel()->getId();
        return redirect()->route("filament.{$painel}.filament-2fa.login");
    }
}
