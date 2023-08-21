@props(['action'])
<x-filament-panels::page.simple>
    <x-filament-panels::form wire:submit.prevent="{{$action}}">
        {{$slot}}
    </x-filament-panels::form>
</x-filament-panels::page.simple>
