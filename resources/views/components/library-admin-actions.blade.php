@props(['item'])

@if(auth()->check() && auth()->user()->is_admin)
    <div class="flex items-center gap-1">
        <a href="{{ route('library.edit', $item->id) }}" class="rounded p-1.5 text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100" title="{{ __('Edit') }}">
            <flux:icon.pencil-square class="size-4" />
        </a>
        <form action="{{ route('library.destroy', $item->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this item?') }}');">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded p-1.5 text-zinc-500 hover:bg-red-50 hover:text-red-700 dark:text-zinc-400 dark:hover:bg-red-950 dark:hover:text-red-300" title="{{ __('Delete') }}">
                <flux:icon.trash class="size-4" />
            </button>
        </form>
    </div>
@endif
