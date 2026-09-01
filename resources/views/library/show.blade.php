<x-app-layout>
    @php
        $documentFiles = $item->files->where('type', 'document');
        $videoFiles = $item->files->where('type', 'video');
    @endphp

    <main class="py-5 sm:py-7">
        <div class="app-container">
            <nav class="mb-4 flex items-center justify-between gap-3" aria-label="{{ __('Breadcrumb') }}">
                <a href="{{ route('library.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-zinc-600 hover:text-primary-700 dark:text-zinc-400 dark:hover:text-primary-300">
                    <flux:icon.arrow-left class="size-4" />
                    {{ __('Back to Library') }}
                </a>
                @if(auth()->check() && auth()->user()->is_admin)
                    <a href="{{ route('library.edit', $item->id) }}" class="btn-secondary">
                        <flux:icon.pencil-square class="me-1.5 size-4" />
                        {{ __('Edit') }}
                    </a>
                @endif
            </nav>

            <header class="mb-5 border-b border-zinc-300 pb-5 dark:border-zinc-700">
                <div class="flex flex-wrap items-center gap-2 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                    @if(count($item->categories) > 0)
                        <span class="text-primary-700 dark:text-primary-300">{{ implode(', ', $item->categories) }}</span>
                    @endif
                    <time datetime="{{ $item->created_at->toDateString() }}">{{ $item->created_at->format('d.m.Y') }}</time>
                    @if(auth()->check() && auth()->user()->is_admin && !$item->is_published)
                        <span class="rounded border border-zinc-300 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide dark:border-zinc-700">{{ __('Draft') }}</span>
                    @endif
                </div>
                <h1 class="mt-2 max-w-4xl text-2xl font-semibold leading-tight tracking-tight text-zinc-950 sm:text-3xl dark:text-zinc-50">{{ $item->title }}</h1>
                @if($item->description)
                    <p class="mt-3 max-w-4xl whitespace-pre-line text-[15px] leading-6 text-zinc-700 dark:text-zinc-300">{{ $item->description }}</p>
                @endif
            </header>

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_280px]">
                <div class="space-y-6">
                    @if($documentFiles->count() > 0)
                        <section aria-labelledby="documents-heading">
                            <div class="mb-2 flex items-center justify-between">
                                <h2 id="documents-heading" class="text-base font-semibold text-zinc-950 dark:text-zinc-50">{{ __('app.documents') }}</h2>
                                <span class="text-xs tabular-nums text-zinc-500 dark:text-zinc-400">{{ $documentFiles->count() }}</span>
                            </div>
                            <div class="overflow-hidden border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900" style="border-radius: 8px;">
                                @foreach($documentFiles as $file)
                                    <div class="flex flex-col gap-3 p-3 sm:flex-row sm:items-center sm:justify-between sm:px-4 {{ !$loop->last ? 'border-b border-zinc-200 dark:border-zinc-800' : '' }}">
                                        <div class="flex min-w-0 items-center gap-3">
                                            <div class="flex size-9 shrink-0 items-center justify-center rounded-md bg-primary-100 text-primary-800 dark:bg-primary-900/50 dark:text-primary-200">
                                                <flux:icon.document-text class="size-4.5" />
                                            </div>
                                            <div class="min-w-0">
                                                <h3 class="truncate text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $file->name }}</h3>
                                                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                                                    {{ strtoupper(pathinfo($file->original_filename, PATHINFO_EXTENSION)) }}
                                                    @if($file->formatted_file_size)
                                                        <span class="mx-1">/</span>{{ $file->formatted_file_size }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <a href="{{ route('library.file.download', $file->id) }}" class="btn-primary w-full shrink-0 sm:w-auto">
                                            <flux:icon.arrow-down-tray class="me-1.5 size-4" />
                                            {{ __('app.download') }}
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if($videoFiles->count() > 0)
                        <section aria-labelledby="videos-heading">
                            <div class="mb-2 flex items-center justify-between">
                                <h2 id="videos-heading" class="text-base font-semibold text-zinc-950 dark:text-zinc-50">{{ __('app.videos') }}</h2>
                                <span class="text-xs tabular-nums text-zinc-500 dark:text-zinc-400">{{ $videoFiles->count() }}</span>
                            </div>
                            <div class="grid gap-4 md:grid-cols-2">
                                @foreach($videoFiles as $video)
                                    <article class="overflow-hidden border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900" style="border-radius: 8px;">
                                        @if(Str::contains($video->external_url, 'youtube.com') || Str::contains($video->external_url, 'youtu.be'))
                                            @php
                                                $videoId = '';
                                                if (Str::contains($video->external_url, 'youtube.com/watch?v=')) {
                                                    $videoId = explode('v=', $video->external_url)[1];
                                                    $videoId = explode('&', $videoId)[0];
                                                } elseif (Str::contains($video->external_url, 'youtu.be/')) {
                                                    $videoId = explode('youtu.be/', $video->external_url)[1];
                                                    $videoId = explode('?', $videoId)[0];
                                                }
                                            @endphp
                                            <div class="aspect-video bg-zinc-100 dark:bg-zinc-800">
                                                <iframe src="https://www.youtube.com/embed/{{ $videoId }}" class="h-full w-full" title="{{ $video->name }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                            </div>
                                        @elseif(Str::contains($video->external_url, 'vimeo.com'))
                                            @php
                                                preg_match('/vimeo\.com\/([0-9]+)/', $video->external_url, $matches);
                                                $videoId = $matches[1] ?? '';
                                            @endphp
                                            <div class="aspect-video bg-zinc-100 dark:bg-zinc-800">
                                                <iframe src="https://player.vimeo.com/video/{{ $videoId }}" class="h-full w-full" title="{{ $video->name }}" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                                            </div>
                                        @else
                                            <div class="flex aspect-video items-center justify-center bg-zinc-100 dark:bg-zinc-800">
                                                <flux:icon.video-camera class="size-8 text-zinc-400" />
                                            </div>
                                        @endif
                                        <div class="flex items-center justify-between gap-3 p-3">
                                            <h3 class="truncate text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $video->name }}</h3>
                                            <a href="{{ $video->external_url }}" target="_blank" rel="noopener noreferrer" class="shrink-0 text-xs font-semibold text-primary-700 hover:text-primary-900 dark:text-primary-300 dark:hover:text-primary-200">{{ __('Open video') }}</a>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if($item->files->count() === 0)
                        <div class="border border-zinc-200 bg-white px-5 py-10 text-center dark:border-zinc-800 dark:bg-zinc-900" style="border-radius: 8px;">
                            <flux:icon.document class="mx-auto size-8 text-zinc-400" />
                            <h2 class="mt-3 text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ __('app.no_files') }}</h2>
                            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ __('No documents or videos have been uploaded for this item') }}</p>
                        </div>
                    @endif
                </div>

                <aside class="space-y-5 lg:border-s lg:border-zinc-200 lg:ps-6 dark:lg:border-zinc-800">
                    <section>
                        <h2 class="text-xs font-semibold uppercase tracking-[0.1em] text-zinc-500 dark:text-zinc-400">{{ __('Resource details') }}</h2>
                        <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-4 text-sm">
                            <div>
                                <dt class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('app.documents') }}</dt>
                                <dd class="mt-0.5 font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">{{ $documentFiles->count() }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('app.videos') }}</dt>
                                <dd class="mt-0.5 font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">{{ $videoFiles->count() }}</dd>
                            </div>
                            <div class="col-span-2">
                                <dt class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('app.updated_at') }}</dt>
                                <dd class="mt-0.5 font-medium text-zinc-900 dark:text-zinc-100">{{ $item->updated_at->format('d.m.Y') }}</dd>
                            </div>
                        </dl>
                    </section>

                    @if(count($item->categories) > 0)
                        <section class="border-t border-zinc-200 pt-4 dark:border-zinc-800">
                            <h2 class="text-xs font-semibold uppercase tracking-[0.1em] text-zinc-500 dark:text-zinc-400">{{ __('Categories') }}</h2>
                            <div class="mt-2 space-y-1">
                                @foreach($item->categories as $category)
                                    <a href="{{ route('library.category', $category) }}" class="block rounded-md px-2 py-1.5 text-sm font-semibold text-primary-800 hover:bg-primary-100 dark:text-primary-200 dark:hover:bg-primary-900/40">{{ $category }}</a>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if(count($item->tags) > 0)
                        <section class="border-t border-zinc-200 pt-4 dark:border-zinc-800">
                            <h2 class="text-xs font-semibold uppercase tracking-[0.1em] text-zinc-500 dark:text-zinc-400">{{ __('Tags') }}</h2>
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                @foreach($item->tags as $tag)
                                    <a href="{{ route('library.tag', $tag) }}" class="rounded-md border border-zinc-300 bg-white px-2 py-1 text-xs font-medium text-zinc-700 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800">{{ $tag }}</a>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </aside>
            </div>
        </div>
    </main>
</x-app-layout>
