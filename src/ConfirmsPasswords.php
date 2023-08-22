<?php

declare(strict_types=1);

namespace TFSThiagoBR98\FilamentTwoFactor;

use Filament\Facades\Filament;
use Illuminate\Validation\ValidationException;

trait ConfirmsPasswords
{
    /**
     * Indicates if the user's password is being confirmed.
     *
     * @var bool
     */
    public bool $confirmingPassword = false;

    /**
     * The ID of the operation being confirmed.
     *
     * @var string|null
     */
    public ?string $confirmableId = null;

    /**
     * The user's password.
     *
     * @var string
     */
    public string $confirmablePassword = '';

    /**
     * Start confirming the user's password.
     *
     * @param  string  $confirmableId
     * @return mixed
     */
    public function startConfirmingPassword(string $confirmableId): mixed
    {
        $this->resetErrorBag();

        if ($this->passwordIsConfirmed()) {
            return $this->dispatch('password-confirmed', [
                'id' => $confirmableId,
            ]);
        }

        $this->confirmingPassword = true;
        $this->confirmableId = $confirmableId;
        $this->confirmablePassword = '';
        $this->dispatch('open-modal', [
            'id' => 'confirm-password',
        ]);

        $this->dispatch('confirming-password');
    }

    /**
     * Stop confirming the user's password.
     *
     * @return void
     */
    public function stopConfirmingPassword(): void
    {
        $this->confirmingPassword = false;
        $this->confirmableId = null;
        $this->confirmablePassword = '';
        $this->dispatch('close-modal', [
            'id' => 'confirm-password',
        ]);
    }

    /**
     * Confirm the user's password.
     *
     * @return void
     */
    public function confirmPassword(): void
    {
        $guard = Filament::auth();
        if (! $guard->validate([
            'email' => $guard->user()->email,
            'password' => $this->confirmablePassword,
        ])) {
            throw ValidationException::withMessages([
                'confirmable_password' => [__('filament-2fa::two-factor.message.password_not_match')],
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->dispatch('password-confirmed', [
            'id' => $this->confirmableId,
        ]);

        $this->stopConfirmingPassword();
    }

    /**
     * Ensure that the user's password has been recently confirmed.
     *
     * @param  int|null  $maximumSecondsSinceConfirmation
     * @return void
     */
    protected function ensurePasswordIsConfirmed($maximumSecondsSinceConfirmation = null): void
    {
        $maximumSecondsSinceConfirmation = $maximumSecondsSinceConfirmation ?: config('auth.password_timeout', 900);

        $this->passwordIsConfirmed($maximumSecondsSinceConfirmation) ? null : abort(403);
    }

    /**
     * Determine if the user's password has been recently confirmed.
     *
     * @param  int|null  $maximumSecondsSinceConfirmation
     * @return bool
     */
    protected function passwordIsConfirmed($maximumSecondsSinceConfirmation = null): bool
    {
        $maximumSecondsSinceConfirmation = $maximumSecondsSinceConfirmation ?: config('auth.password_timeout', 900);

        return (time() - session('auth.password_confirmed_at', 0)) < $maximumSecondsSinceConfirmation;
    }
}
