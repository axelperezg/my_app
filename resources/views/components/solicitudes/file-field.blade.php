@props(['name', 'label', 'accept' => null, 'multiple' => false])

<div>
    <flux:label>{{ $label }}</flux:label>

    <input
        type="file"
        wire:model="{{ $name }}"
        @if ($accept) accept="{{ $accept }}" @endif
        @if ($multiple) multiple @endif
        {{ $attributes->class([
            'block w-full text-sm text-zinc-700 dark:text-zinc-300',
            'file:mr-4 file:rounded-lg file:border-0 file:bg-zinc-100 file:px-4 file:py-2',
            'file:text-sm file:font-medium hover:file:bg-zinc-200',
            'dark:file:bg-zinc-700 dark:hover:file:bg-zinc-600',
        ]) }}
    />

    <div wire:loading wire:target="{{ $name }}" class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
        {{ __('Subiendo...') }}
    </div>

    <flux:error :name="$name" />

    @error($name.'.*')
        <flux:text class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
    @enderror
</div>
