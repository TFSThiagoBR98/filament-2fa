<?php

namespace Webbingbrasil\FilamentTwoFactor\Http\Livewire\Auth;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Webbingbrasil\FilamentTwoFactor\FilamentTwoFactor;

class TwoFactorChallenge extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public $code;
    public $recovery_code;
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
    protected $remember;

    public function mount()
    {
        $user = $this->challengedUser();
        if (! $user->hasTwoFactorEnabled() ) {
            $this->redirectRoute('filament.auth.login');
        }
    }

    protected function getCodeFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('code')
                ->label(__('filament-2fa::two-factor.field.code'))
                ->rules('nullable|string'),
        ];
    }

    protected function getRecoveryFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('recovery_code')
                ->label(__('filament-2fa::two-factor.field.recovery_code'))
                ->rules('nullable|string'),
        ];
    }

    protected function getForms(): array
    {
        return [
            'formTwoFactorCode' => $this->makeForm()
                ->schema($this->getCodeFormSchema()),
            'formTwoFactorRecovery' => $this->makeForm()
                ->schema($this->getRecoveryFormSchema()),
        ];
    }

    public function hasChallengedUser()
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
    public function challengedUser()
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
    public function hasValidCode()
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
     * @return string|void
     */
    public function validRecoveryCode()
    {
        if (! $this->recovery_code) {
            return;
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
    public function remember()
    {
        if (! $this->remember) {
            $this->remember = request()->session()->pull('login.remember', false);
        }

        return $this->remember;
    }

    public function verify()
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

    public function render(): View
    {
        $view = view('filament-2fa::two-factor-challenge');

        $view->layout('filament::components.layouts.base', [
            'title' => __('filament-2fa::two-factor.title'),
        ]);

        return $view;
    }
}
