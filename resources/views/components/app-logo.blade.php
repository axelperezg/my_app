@props([
    'sidebar' => false,
])

@php
    $logoUrl = \App\Models\Configuracion::actual()->logoAppUrl();
@endphp

@if($sidebar)
    <flux:sidebar.brand :name="config('app.name', 'Laravel')" {{ $attributes }}>
        @if ($logoUrl)
            <x-slot name="logo">
                <img src="{{ $logoUrl }}" alt="" class="size-8 rounded-md object-contain">
            </x-slot>
        @else
            <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
                <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
            </x-slot>
        @endif
    </flux:sidebar.brand>
@else
    <flux:brand :name="config('app.name', 'Laravel')" {{ $attributes }}>
        @if ($logoUrl)
            <x-slot name="logo">
                <img src="{{ $logoUrl }}" alt="" class="size-8 rounded-md object-contain">
            </x-slot>
        @else
            <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
                <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
            </x-slot>
        @endif
    </flux:brand>
@endif
