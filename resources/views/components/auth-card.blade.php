@props(['action'])
<x-filament-panels::page.simple>
    <x-filament-panels::form wire:submit="{{$action}}">
        {{$slot}}
    </x-filament-panels::form>
</x-filament-panels::page.simple>
