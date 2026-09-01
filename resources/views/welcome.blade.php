<x-app-layout>
    <main class="py-8 sm:py-12">
        <div class="app-container">
            <section class="grid min-h-[calc(100dvh-10rem)] items-center gap-8 lg:grid-cols-[minmax(0,1.2fr)_minmax(280px,0.8fr)]">
                <div class="max-w-3xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-primary-700 dark:text-primary-300">{{ __('Association professional library') }}</p>
                    <h1 class="mt-3 text-4xl font-semibold leading-[1.05] tracking-tight text-zinc-950 sm:text-5xl lg:text-6xl dark:text-zinc-50">
                        {{ __('EMDR resources for clinical practice') }}
                    </h1>
                    <p class="mt-5 max-w-xl text-base leading-7 text-zinc-700 sm:text-lg dark:text-zinc-300">
                        {{ __('Protocols, worksheets, presentations, and training materials collected for practicing therapists.') }}
                    </p>

                    <div class="mt-7 flex flex-wrap gap-3">
                        @auth
                            <a href="{{ route('library.index') }}" class="btn-primary">{{ __('Open Library') }}</a>
                            @if(auth()->user()->is_admin)
                                <a href="{{ route('dashboard') }}" class="btn-secondary">{{ __('Dashboard') }}</a>
                            @endif
                        @else
                            <a href="{{ route('library.index') }}" class="btn-primary">{{ __('Access Library') }}</a>
                            <a href="{{ route('login') }}" class="btn-secondary">{{ __('Staff Login') }}</a>
                        @endauth
                    </div>

                    <dl class="mt-10 grid max-w-2xl gap-4 border-t border-zinc-300 pt-5 sm:grid-cols-3 dark:border-zinc-700">
                        <div>
                            <dt class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Clinical materials') }}</dt>
                            <dd class="mt-1 text-sm leading-5 text-zinc-600 dark:text-zinc-400">{{ __('Documents ready for professional use.') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Training video') }}</dt>
                            <dd class="mt-1 text-sm leading-5 text-zinc-600 dark:text-zinc-400">{{ __('Demonstrations and recorded learning.') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Structured access') }}</dt>
                            <dd class="mt-1 text-sm leading-5 text-zinc-600 dark:text-zinc-400">{{ __('Categories and tags for quick retrieval.') }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="flex items-center justify-center border border-zinc-200 bg-white p-8 sm:p-12 dark:border-zinc-800 dark:bg-zinc-900" style="border-radius: 8px;">
                    <img src="{{ asset('logo.png') }}" alt="{{ __('EMDR Association logo') }}" class="h-auto w-full max-w-[320px] object-contain" width="320" height="320">
                </div>
            </section>
        </div>
    </main>
</x-app-layout>
