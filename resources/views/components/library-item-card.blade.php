@props(['item'])

@php
    $documentCount = $item->files->where('type', 'document')->count();
    $videoCount = $item->files->where('type', 'video')->count();
@endphp

<article class="group flex h-full flex-col border border-zinc-200 bg-white p-4 transition-colors hover:border-primary-400 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-primary-700" style="border-radius: 8px;">
    <div class="mb-2 flex items-start justify-between gap-3">
        <a href="{{ route('library.show', $item->id) }}" class="min-w-0">
            <h3 class="text-[15px] font-semibold leading-5 text-zinc-950 group-hover:text-primary-700 dark:text-zinc-50 dark:group-hover:text-primary-300">
                {{ $item->title }}
            </h3>
        </a>
        @if(auth()->check() && auth()->user()->is_admin && !$item->is_published)
            <span class="shrink-0 rounded border border-zinc-300 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-zinc-600 dark:border-zinc-700 dark:text-zinc-400">{{ __('Draft') }}</span>
        @endif
    </div>

    @if($item->description)
        <p class="mb-3 line-clamp-4 text-sm leading-5 text-zinc-600 dark:text-zinc-400">
            {{ $item->description }}
        </p>
    @endif

    <div class="mb-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-zinc-500 dark:text-zinc-400">
        <span class="inline-flex items-center gap-1">
            <flux:icon.document-text class="size-3.5" />
            {{ $documentCount }} {{ __('app.documents') }}
        </span>
        <span class="inline-flex items-center gap-1">
            <flux:icon.video-camera class="size-3.5" />
            {{ $videoCount }} {{ __('app.videos') }}
        </span>
        <time datetime="{{ $item->created_at->toDateString() }}">{{ $item->created_at->format('d.m.Y') }}</time>
    </div>

    <div class="mt-auto space-y-2">
        @if(count($item->categories) > 0)
            <div class="flex flex-wrap gap-1.5">
                @foreach($item->categories as $category)
                    <span class="rounded bg-primary-100 px-2 py-1 text-[11px] font-semibold text-primary-900 dark:bg-primary-900/50 dark:text-primary-100">{{ $category }}</span>
                @endforeach
            </div>
        @endif

        @if(count($item->tags) > 0)
            <p class="line-clamp-1 text-xs text-zinc-500 dark:text-zinc-400">
                {{ collect($item->tags)->map(fn ($tag) => '#'.$tag)->join('  ') }}
            </p>
        @endif

        <div class="flex items-center justify-between border-t border-zinc-200 pt-3 dark:border-zinc-800">
            <a href="{{ route('library.show', $item->id) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-200">
                {{ __('Open resource') }}
                <flux:icon.arrow-right class="size-3.5" />
            </a>
            <x-library-admin-actions :item="$item" />
        </div>
    </div>
</article>
