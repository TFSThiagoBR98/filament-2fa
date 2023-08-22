@props(['title' => __('filament-2fa::two-factor.message.confirm_password'), 'content' => __('filament-2fa::two-factor.message.confirm_password_instructions'), 'button' => __('filament-2fa::two-factor.button.confirm')])

@php
    $confirmableId = md5($attributes->wire('then'));
@endphp

<span
    {{ $attributes->wire('then') }}
    x-data
    x-ref="span"
    x-on:click="$wire.startConfirmingPassword('{{ $confirmableId }}')"
    x-on:password-confirmed.window="setTimeout(() => $event.detail.id === '{{ $confirmableId }}' && $refs.span.dispatchEvent(new CustomEvent('then', { bubbles: false })), 250);"
>
    {{ $slot }}
</span>

@once
    <x-filament::modal id="confirm-password">
        <x-slot name="header">
            {{ $title }}
        </x-slot>

        <p class="text-sm">
            {{ $content }}
        </p>

        <div class="mt-4" x-data="{}" x-on:confirming-password.window="setTimeout(() => $refs.confirmable_password.focus(), 250)">
            <x-filament-forms::field-wrapper>
                <x-filament::input.wrapper :valid="! $errors->has('confirmable_password')">
                    <x-filament::input
                        type="password"
                        placeholder="{{ __('filament-2fa::two-factor.field.password') }}"
                        wire:model="confirmablePassword"
                        x-ref="confirmable_password"
                        wire:keydown.enter="confirmPassword"
                        />
                </x-filament::input.wrapper>
            </x-filament-forms::field-wrapper>
        </div>

        <x-slot name="footer">
            <x-filament::button type="button" color="secondary" wire:click="stopConfirmingPassword" wire:loading.attr="disabled">
                {{ __('filament-2fa::two-factor.button.cancel') }}
            </x-filament::button>

            <x-filament::button type="button" class="ml-3" dusk="confirm-password-button" wire:click="confirmPassword" wire:loading.attr="disabled">
                {{ $button }}
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
@endonce
