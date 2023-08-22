<?php

declare(strict_types=1);

return [
    'enable_two_factor_page' => true,
    'show_two_factor_page_in_user_menu' => true,
    'show_two_factor_page_in_navbar' => false,
    'two_factor_challenge_component_path' => \TFSThiagoBR98\FilamentTwoFactor\Http\Livewire\Auth\TwoFactorChallenge::class,
];
