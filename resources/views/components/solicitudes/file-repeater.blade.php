@props(['name', 'label', 'addLabel', 'items', 'addMethod', 'removeMethod', 'accept' => null])

<div>
    <flux:label>{{ $label }}</flux:label>

    <div class="mt-2 space-y-3">
        @foreach ($items as $index => $item)
            <div class="flex items-center gap-2" wire:key="{{ $name }}-{{ $index }}">
                <input
                    type="file"
                    wire:model="{{ $name }}.{{ $index }}"
                    @if ($accept) accept="{{ $accept }}" @endif
                    class="block w-full text-sm text-zinc-700 dark:text-zinc-300 file:mr-4 file:rounded-lg file:border-0 file:bg-zinc-100 file:px-4 file:py-2 file:text-sm file:font-medium hover:file:bg-zinc-200 dark:file:bg-zinc-700 dark:hover:file:bg-zinc-600"
                />

                @if (count($items) > 1)
                    <flux:button type="button" variant="ghost" size="sm" icon="trash" wire:click="{{ $removeMethod }}({{ $index }})" />
                @endif
            </div>

            <div wire:loading wire:target="{{ $name }}.{{ $index }}" class="text-xs text-zinc-500 dark:text-zinc-400">
                {{ __('Subiendo...') }}
            </div>

            <flux:error name="{{ $name }}.{{ $index }}" />
        @endforeach
    </div>

    <flux:button type="button" variant="ghost" size="sm" icon="plus" class="mt-2" wire:click="{{ $addMethod }}">
        {{ $addLabel }}
    </flux:button>
</div>
