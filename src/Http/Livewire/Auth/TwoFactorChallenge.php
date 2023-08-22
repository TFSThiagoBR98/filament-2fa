<?php

declare(strict_types=1);

namespace TFSThiagoBR98\FilamentTwoFactor\Http\Livewire\Auth;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Pages\SimplePage;

class TwoFactorChallenge extends SimplePage
{
    use InteractsWithFormActions;

    /**
     * @var view-string
     */
    protected static string $view = 'filament-2fa::two-factor-challenge';

    public ?string $code = null;
    public ?string $recovery_code = null;
    public bool $isRecovery = false;

    /**
     * The user attempting the two factor challenge.
     *
     * @var mixed
     */
    protected $challengedUser;

    /**
     * Indicates if the user wished to be remembered after login.
     *
     * @var bool
     */
    protected bool $remember = false;

    /**
     * @return void
     */
    public function mount(): void
    {
        $user = $this->challengedUser();
        if (! $user->hasTwoFactorEnabled() ) {
            $this->redirectRoute('filament.auth.login');
        }
    }

    /**
     * @return array<int,Forms\Components\TextInput>
     */
    protected function getCodeFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('code')
                ->label(__('filament-2fa::two-factor.field.code'))
                ->rules('nullable|string'),
        ];
    }

    /**
     * @return array<int,Forms\Components\TextInput>
     */
    protected function getRecoveryFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('recovery_code')
                ->label(__('filament-2fa::two-factor.field.recovery_code'))
                ->rules('nullable|string'),
        ];
    }

    /**
     * @return array<string,Forms\Form>
     */
    protected function getForms(): array
    {
        return [
            'formTwoFactorCode' => $this->makeForm()
                ->schema($this->getCodeFormSchema()),
            'formTwoFactorRecovery' => $this->makeForm()
                ->schema($this->getRecoveryFormSchema()),
        ];
    }

    /**
     * @return bool
     */
    public function hasChallengedUser(): bool
    {
        if ($this->challengedUser) {
            return true;
        }

        $model = Filament::auth()->getProvider()->getModel();

        return request()->session()->has('login.id') &&
            $model::find(request()->session()->get('login.id'));
    }

    /**
     * Get the user that is attempting the two factor challenge.
     *
     * @return mixed
     */
    public function challengedUser(): mixed
    {
        if ($this->challengedUser) {
            return $this->challengedUser;
        }

        $model = Filament::auth()->getProvider()->getModel();

        if (! request()->session()->has('login.id') ||
            ! $user = $model::find(request()->session()->get('login.id'))) {
            $this->redirectRoute('filament.auth.login');
        }

        return $this->challengedUser = $user;
    }

    /**
     * Determine if the request has a valid two factor code.
     *
     * @return bool
     */
    public function hasValidCode(): bool
    {
        return $this->code && tap($this->challengedUser()->validateTwoFactorCode(
            $this->code
        ), function ($result) {
            if ($result) {
                request()->session()->forget('login.id');
                return;
            }

            $this->addError('code', __('filament-2fa::two-factor.message.invalid_code'));
        });
    }

    /**
     * Get the valid recovery code if one exists on the request.
     *
     * @return bool
     */
    public function validRecoveryCode(): bool
    {
        if (! $this->recovery_code) {
            return false;
        }

        return tap($this->challengedUser()->validateTwoFactorCode(
            $this->recovery_code, true
        ), function ($result) {
            if ($result) {
                request()->session()->forget('login.id');
                return;
            }

            $this->addError('recovery_code', __('filament-2fa::two-factor.message.invalid_recovery_code'));
        });
    }

    /**
     * Determine if the user wanted to be remembered after login.
     *
     * @return bool
     */
    public function remember(): bool
    {
        if (! $this->remember) {
            $this->remember = request()->session()->pull('login.remember', false);
        }

        return $this->remember;
    }

    /**
     * @return null|LoginResponse
     */
    public function verify(): ?LoginResponse
    {
        $user = $this->challengedUser();
        $this->validate();

        if ($code = $this->validRecoveryCode()) {
            $user->replaceRecoveryCode($code);
        } elseif (! $this->hasValidCode()) {
            return null;
        }

        Filament::auth()->login($user, $this->remember());

        request()->session()->regenerate();

        return app(LoginResponse::class);
    }

    public function getTitle(): string | Htmlable
    {
        return __('filament-2fa::two-factor.title');
    }

    public function getHeading(): string | Htmlable
    {
        return __('filament-panels::pages/auth/login.heading');
    }
}
