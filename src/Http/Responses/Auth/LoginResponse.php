<?php

namespace TFSThiagoBR98\FilamentTwoFactor\Http\Responses\Auth;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector as Redirector;

class LoginResponse implements Responsable
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        return redirect()->route('filament-2fa.login');
    }
}
