@props(['name', 'label', 'description' => null, 'url' => null, 'quitar' => null])

<div>
    <flux:label>{{ $label }}</flux:label>

    @if ($description)
        <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $description }}</flux:text>
    @endif

    <div class="mt-3 flex items-center gap-4">
        @if ($url)
            <img src="{{ $url }}" alt="" class="h-16 w-auto rounded border border-zinc-200 bg-white p-1 dark:border-zinc-700">
        @else
            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded border border-dashed border-zinc-300 text-xs text-zinc-400 dark:border-zinc-600">
                {{ __('Sin logo') }}
            </div>
        @endif

        <div class="flex-1">
            <input
                type="file"
                wire:model="{{ $name }}"
                accept="image/png,image/jpeg,image/webp"
                class="block w-full text-sm text-zinc-700 dark:text-zinc-300 file:mr-4 file:rounded-lg file:border-0 file:bg-zinc-100 file:px-4 file:py-2 file:text-sm file:font-medium hover:file:bg-zinc-200 dark:file:bg-zinc-700 dark:hover:file:bg-zinc-600"
            />

            <div wire:loading wire:target="{{ $name }}" class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                {{ __('Subiendo...') }}
            </div>

            <flux:error :name="$name" />
        </div>

        @if ($url && $quitar)
            <flux:button type="button" variant="ghost" size="sm" icon="trash" wire:click="{{ $quitar }}">
                {{ __('Quitar') }}
            </flux:button>
        @endif
    </div>
</div>
