<x-app-layout>
    <main class="py-5 sm:py-7">
        <div class="app-container">
            <header class="mb-5 border-b border-zinc-300 pb-5 dark:border-zinc-700">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="mb-1 text-xs font-semibold uppercase tracking-[0.12em] text-primary-700 dark:text-primary-300">
                            {{ auth()->check() && auth()->user()->is_admin ? __('Administration') : __('Professional collection') }}
                        </p>
                        <h1 class="text-2xl font-semibold tracking-tight text-zinc-950 sm:text-3xl dark:text-zinc-50">
                            {{ auth()->check() && auth()->user()->is_admin ? __('Manage Library') : __('EMDR Therapist Library') }}
                        </h1>
                        <p class="mt-1 max-w-2xl text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                            {{ __('Protocols, worksheets, presentations, and training materials for clinical practice.') }}
                        </p>
                    </div>

                    @if(auth()->check() && auth()->user()->is_admin)
                        <a href="{{ route('library.create') }}" class="btn-primary self-start lg:self-auto">
                            <flux:icon.plus class="me-1.5 size-4" />
                            {{ __('Add New Item') }}
                        </a>
                    @endif
                </div>
            </header>

            @if (session('error'))
                <div class="mb-4 rounded-md border border-red-300 bg-red-50 px-3 py-2.5 text-sm font-medium text-red-800 dark:border-red-900 dark:bg-red-950/50 dark:text-red-200" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 rounded-md border border-primary-300 bg-primary-50 px-3 py-2.5 text-sm font-medium text-primary-900 dark:border-primary-800 dark:bg-primary-950/50 dark:text-primary-200" role="status">
                    {{ session('success') }}
                </div>
            @endif

            <form method="GET" action="{{ route('library.index') }}" class="mb-5 grid gap-2 border border-zinc-200 bg-white p-3 sm:grid-cols-[minmax(0,1fr)_auto] dark:border-zinc-800 dark:bg-zinc-900" style="border-radius: 8px;">
                @if($activeCategory)
                    <input type="hidden" name="category" value="{{ $activeCategory }}">
                @endif
                @if($activeTag)
                    <input type="hidden" name="tag" value="{{ $activeTag }}">
                @endif
                <label class="sr-only" for="library-search">{{ __('Search library') }}</label>
                <div class="relative">
                    <flux:icon.magnifying-glass class="pointer-events-none absolute start-3 top-1/2 size-4 -translate-y-1/2 text-zinc-500" />
                    <input
                        id="library-search"
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="{{ __('Search by resource title') }}"
                        class="h-10 w-full rounded-md border border-zinc-300 bg-white ps-9 pe-3 text-sm text-zinc-950 placeholder:text-zinc-500 focus:border-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-600/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:focus:border-primary-400"
                    />
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn-primary flex-1 sm:flex-none">{{ __('Search') }}</button>
                    @if(request()->filled('search') || $activeCategory || $activeTag)
                        <a href="{{ route('library.index') }}" class="btn-secondary flex-1 sm:flex-none">{{ __('Reset') }}</a>
                    @endif
                </div>
            </form>

            <div class="grid gap-5 lg:grid-cols-[220px_minmax(0,1fr)] xl:grid-cols-[240px_minmax(0,1fr)]">
                <aside class="space-y-5 lg:sticky lg:top-20 lg:self-start" aria-label="{{ __('Library filters') }}">
                    <section>
                        <h2 class="mb-2 text-xs font-semibold uppercase tracking-[0.1em] text-zinc-500 dark:text-zinc-400">{{ __('Categories') }}</h2>
                        <nav class="space-y-0.5">
                            @foreach($categories as $category)
                                <a href="{{ route('library.index', array_filter(['category' => $category, 'search' => request('search')])) }}"
                                   class="flex items-center justify-between rounded-md px-2.5 py-2 text-sm font-medium transition-colors {{ $activeCategory === $category ? 'bg-primary-100 text-primary-900 dark:bg-primary-900/60 dark:text-primary-100' : 'text-zinc-700 hover:bg-zinc-200/70 dark:text-zinc-300 dark:hover:bg-zinc-800' }}">
                                    <span class="truncate">{{ $category }}</span>
                                    @if($activeCategory === $category)
                                        <flux:icon.check class="size-3.5 shrink-0" />
                                    @endif
                                </a>
                            @endforeach
                        </nav>
                    </section>

                    @if(count($tags) > 0)
                        <section class="border-t border-zinc-200 pt-4 dark:border-zinc-800">
                            <h2 class="mb-2 text-xs font-semibold uppercase tracking-[0.1em] text-zinc-500 dark:text-zinc-400">{{ __('Tags') }}</h2>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($tags as $tag)
                                    <a href="{{ route('library.index', array_filter(['category' => $activeCategory, 'tag' => $tag, 'search' => request('search')])) }}"
                                       class="rounded-md border px-2 py-1 text-xs font-medium transition-colors {{ $activeTag === $tag ? 'border-primary-700 bg-primary-700 text-white dark:border-primary-300 dark:bg-primary-300 dark:text-primary-950' : 'border-zinc-300 bg-white text-zinc-700 hover:border-zinc-400 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:border-zinc-600 dark:hover:bg-zinc-800' }}">
                                        {{ $tag }}
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </aside>

                <section aria-labelledby="results-heading">
                    <div class="mb-3 flex items-center justify-between">
                        <h2 id="results-heading" class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ __('Resources') }}
                        </h2>
                        <span class="text-xs tabular-nums text-zinc-500 dark:text-zinc-400">
                            {{ $items->total() }} {{ __('items') }}
                        </span>
                    </div>

                    @if($items->isEmpty())
                        <div class="border border-zinc-200 bg-white px-5 py-12 text-center dark:border-zinc-800 dark:bg-zinc-900" style="border-radius: 8px;">
                            <flux:icon.document-magnifying-glass class="mx-auto size-8 text-zinc-400" />
                            <h3 class="mt-3 text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ __('No items found') }}</h3>
                            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Try adjusting your search or filters') }}</p>
                            <a href="{{ route('library.index') }}" class="btn-secondary mt-4">{{ __('Clear filters') }}</a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-3 xl:grid-cols-2">
                            @foreach($items as $item)
                                <x-library-item-card :item="$item" />
                            @endforeach
                        </div>

                        <div class="mt-5 border-t border-zinc-200 pt-4 dark:border-zinc-800">
                            {{ $items->appends(request()->only('search', 'category', 'tag'))->links() }}
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </main>
</x-app-layout>
