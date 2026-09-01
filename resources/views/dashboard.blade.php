<x-layouts.app :title="__('Dashboard')">
    <main class="py-5 sm:py-7">
        <div class="app-container">
            <header class="mb-6 border-b border-zinc-300 pb-5 dark:border-zinc-700">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-primary-700 dark:text-primary-300">{{ __('Administration') }}</p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight text-zinc-950 sm:text-3xl dark:text-zinc-50">{{ __('Library operations') }}</h1>
                <p class="mt-1 max-w-2xl text-sm leading-6 text-zinc-600 dark:text-zinc-400">{{ __('Manage resources, organize taxonomy, and review library usage.') }}</p>
            </header>

            <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
                <div>
                    <h2 class="mb-2 text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Primary actions') }}</h2>
                    <div class="overflow-hidden border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900" style="border-radius: 8px;">
                        <a href="{{ route('library.create') }}" class="group grid gap-3 border-b border-zinc-200 p-4 hover:bg-zinc-50 sm:grid-cols-[36px_minmax(0,1fr)_auto] sm:items-center dark:border-zinc-800 dark:hover:bg-zinc-800/60">
                            <span class="flex size-9 items-center justify-center rounded-md bg-primary-100 text-primary-800 dark:bg-primary-900/50 dark:text-primary-200"><flux:icon.plus class="size-4" /></span>
                            <span>
                                <span class="block text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Add library item') }}</span>
                                <span class="mt-0.5 block text-sm text-zinc-600 dark:text-zinc-400">{{ __('Upload documents or add training video links.') }}</span>
                            </span>
                            <flux:icon.arrow-right class="hidden size-4 text-zinc-400 sm:block" />
                        </a>
                        <a href="{{ route('library.index') }}" class="group grid gap-3 border-b border-zinc-200 p-4 hover:bg-zinc-50 sm:grid-cols-[36px_minmax(0,1fr)_auto] sm:items-center dark:border-zinc-800 dark:hover:bg-zinc-800/60">
                            <span class="flex size-9 items-center justify-center rounded-md bg-primary-100 text-primary-800 dark:bg-primary-900/50 dark:text-primary-200"><flux:icon.book-open class="size-4" /></span>
                            <span>
                                <span class="block text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Manage resources') }}</span>
                                <span class="mt-0.5 block text-sm text-zinc-600 dark:text-zinc-400">{{ __('Review, edit, publish, or remove library content.') }}</span>
                            </span>
                            <flux:icon.arrow-right class="hidden size-4 text-zinc-400 sm:block" />
                        </a>
                        <a href="{{ route('admin.taxonomy.index') }}" class="group grid gap-3 border-b border-zinc-200 p-4 hover:bg-zinc-50 sm:grid-cols-[36px_minmax(0,1fr)_auto] sm:items-center dark:border-zinc-800 dark:hover:bg-zinc-800/60">
                            <span class="flex size-9 items-center justify-center rounded-md bg-primary-100 text-primary-800 dark:bg-primary-900/50 dark:text-primary-200"><flux:icon.tag class="size-4" /></span>
                            <span>
                                <span class="block text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Organize taxonomy') }}</span>
                                <span class="mt-0.5 block text-sm text-zinc-600 dark:text-zinc-400">{{ __('Maintain categories and tags used by therapists.') }}</span>
                            </span>
                            <flux:icon.arrow-right class="hidden size-4 text-zinc-400 sm:block" />
                        </a>
                        <a href="{{ route('admin.stats') }}" class="group grid gap-3 p-4 hover:bg-zinc-50 sm:grid-cols-[36px_minmax(0,1fr)_auto] sm:items-center dark:hover:bg-zinc-800/60">
                            <span class="flex size-9 items-center justify-center rounded-md bg-primary-100 text-primary-800 dark:bg-primary-900/50 dark:text-primary-200"><flux:icon.chart-bar class="size-4" /></span>
                            <span>
                                <span class="block text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Review statistics') }}</span>
                                <span class="mt-0.5 block text-sm text-zinc-600 dark:text-zinc-400">{{ __('Inspect visits, downloads, and popular resources.') }}</span>
                            </span>
                            <flux:icon.arrow-right class="hidden size-4 text-zinc-400 sm:block" />
                        </a>
                    </div>
                </div>

                <aside class="border-s-0 border-zinc-200 lg:border-s lg:ps-6 dark:border-zinc-800">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Working principles') }}</h2>
                    <ul class="mt-3 space-y-4 text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                        <li><strong class="block text-zinc-900 dark:text-zinc-100">{{ __('Publish deliberately') }}</strong>{{ __('Draft items remain visible only to administrators.') }}</li>
                        <li><strong class="block text-zinc-900 dark:text-zinc-100">{{ __('Use clear titles') }}</strong>{{ __('Therapists should understand a resource before opening it.') }}</li>
                        <li><strong class="block text-zinc-900 dark:text-zinc-100">{{ __('Keep taxonomy focused') }}</strong>{{ __('Prefer established categories and precise tags.') }}</li>
                    </ul>
                </aside>
            </section>
        </div>
    </main>
</x-layouts.app>
