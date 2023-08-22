<?php

declare(strict_types=1);

namespace TFSThiagoBR98\FilamentTwoFactor\Http\Livewire;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Notifications\Notification;
use Laragear\TwoFactor\Contracts\TwoFactorAuthenticatable;
use Laragear\TwoFactor\Models\TwoFactorAuthentication;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use TFSThiagoBR98\FilamentTwoFactor\ConfirmsPasswords;

class TwoFactorAuthenticationForm extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    use ConfirmsPasswords;

    /**
     * Indicates if two factor authentication recovery codes are being displayed.
     *
     * @var bool
     */
    public bool $showingRecoveryCodes = false;

    /**
     * The two factor authentication provider.
     *
     * @var \Laragear\TwoFactor\Models\TwoFactorAuthentication
     */
    public ?TwoFactorAuthentication $totp = null;

    /**
     * The OTP code for confirming two factor authentication.
     *
     * @var string|null
     */
    public ?string $code = null;

    /**
     * User record
     *
     * @var mixed
     */
    public mixed $record = null;

    /**
     * @return array<int,Forms\Components\Component>
     */
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('code')
                ->label(__('filament-2fa::two-factor.field.code'))
                ->numeric()
                ->rules('nullable|string'),
        ];
    }

    /**
     * Enable two factor authentication for the user.
     *
     * @return void
     */
    public function enableTwoFactorAuthentication(): void
    {
        $this->ensurePasswordIsConfirmed();

        $this->totp = $this->user->createTwoFactorAuth();
        $this->totp->save();
    }

    /**
     * Confirm two factor authentication for the user.
     *
     * @return void
     */
    public function confirmTwoFactorAuthentication(): void
    {
        $this->ensurePasswordIsConfirmed();

        if (empty($this->code) || !$this->user->confirmTwoFactorAuth($this->code)) {
            Notification::make()
                ->title(__('filament-2fa::two-factor.message.invalid_code'))
                ->body(null)
                ->danger()
                ->send();

            return;
        }

        $this->user->getRecoveryCodes();

        $this->showingRecoveryCodes = true;
    }

    /**
     * Display the user's recovery codes.
     *
     * @return void
     */
    public function showRecoveryCodes(): void
    {
        $this->ensurePasswordIsConfirmed();
        $this->showingRecoveryCodes = true;
    }

    /**
     * Generate new recovery codes for the user.
     *
     * @return void
     */
    public function regenerateRecoveryCodes(): void
    {
        $this->ensurePasswordIsConfirmed();

        $this->user->generateRecoveryCodes();

        $this->showingRecoveryCodes = true;
    }

    /**
     * Disable two factor authentication for the user.
     *
     * @return void
     */
    public function disableTwoFactorAuthentication(): void
    {
        $this->ensurePasswordIsConfirmed();

        $this->user->disableTwoFactorAuth();

        $this->showingRecoveryCodes = false;
    }

    /**
     * Get the current user of the application.
     *
     * @return mixed
     */
    public function getUserProperty()
    {
        if ($this->record != null && $this->record instanceof TwoFactorAuthenticatable) {
            return $this->record;
        } else {
            return Filament::auth()->user();
        }
    }

    /**
     * Determine if two factor authentication is enabled.
     *
     * @return bool
     */
    public function getEnabledProperty(): bool
    {
        return $this->totp != null || $this->user->hasTwoFactorEnabled();
    }

    /**
     * Determine if two factor authentication is enabled.
     *
     * @return bool
     */
    public function getConfirmedProperty(): bool
    {
        return $this->user->hasTwoFactorEnabled();
    }

    public function render(): View
    {
        return view('filament-2fa::livewire.two-factor-authentication-form');
    }
}
