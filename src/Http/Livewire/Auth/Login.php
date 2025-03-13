<?php

declare(strict_types=1);

namespace TFSThiagoBR98\FilamentTwoFactor\Http\Livewire\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login as FilamentLogin;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as FilamentLoginResponse;
use Illuminate\Validation\ValidationException;
use TFSThiagoBR98\FilamentTwoFactor\Http\Responses\Auth\LoginResponse as TwoFactorLoginResponse;

/**
 * @property ComponentContainer $form
 */
class Login extends FilamentLogin
{
    protected $user;

    public function authenticate(): ?FilamentLoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/login.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();

        $model = Filament::auth()->getProvider()->getModel();
        $this->user = $model::where('email', $data['email'])->first();

        // Alternative Tax Number Login
        if ($this->user == null) {
            $this->user = $model::where('tax_id', $data['email'])->first();
        }

        if ( ! $this->validateCredentials($data)) {
            return null;
        }

        if ($this->user->hasTwoFactorEnabled()) {
            request()->session()->put([
                'login.id' => $this->user->getKey(),
                'login.remember' => $data['remember'],
            ]);

            return app(TwoFactorLoginResponse::class);
        }

        Filament::auth()->login($this->user, $data['remember']);

        return app(FilamentLoginResponse::class);
    }

    protected function validateCredentials(array $data) : bool
    {
        if (! $this->user || ! Filament::auth()->getProvider()->validateCredentials($this->user, ['password' => $data['password']])) {
            throw ValidationException::withMessages([
                'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
            return false;
        }

        return true;
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('CPF ou E-mail')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }
}
