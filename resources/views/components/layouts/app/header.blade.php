<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-[100dvh] bg-zinc-50 dark:bg-zinc-950">
        <div id="app" class="min-h-[100dvh]">
            <flux:header container class="h-16 overflow-visible border-b border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950">
                <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

                <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="ms-1 me-6 shrink-0 lg:ms-0" wire:navigate>
                    <x-app-logo />
                </a>

                <flux:navbar class="-mb-px hidden h-full lg:flex">
                    @auth
                        @if(auth()->user()->is_admin)
                            <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                                {{ __('Dashboard') }}
                            </flux:navbar.item>
                        @endif
                    @endauth
                    <flux:navbar.item icon="book-open" :href="route('library.index')" :current="request()->routeIs('library.*')" wire:navigate>
                        {{ __('Library') }}
                    </flux:navbar.item>
                    @auth
                        @if(auth()->user()->is_admin)
                            <flux:navbar.item icon="tag" :href="route('admin.taxonomy.index')" :current="request()->routeIs('admin.taxonomy.*')" wire:navigate>
                                {{ __('Taxonomy') }}
                            </flux:navbar.item>
                            <flux:navbar.item icon="chart-bar" :href="route('admin.stats')" :current="request()->routeIs('admin.stats*')" wire:navigate>
                                {{ __('Statistics') }}
                            </flux:navbar.item>
                        @endif
                    @endauth
                </flux:navbar>

                <flux:spacer />

                <flux:button
                    x-data
                    x-on:click="$flux.appearance = $flux.appearance === 'dark' ? 'light' : 'dark'"
                    variant="ghost"
                    size="sm"
                    icon="moon"
                    aria-label="{{ __('Toggle color theme') }}"
                />

                <div class="hidden sm:block">
                    <x-language-switcher />
                </div>

                @auth
                    <flux:dropdown position="bottom" align="end">
                        <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

                        <flux:menu class="w-[240px]">
                            <div class="px-2 py-2">
                                <p class="truncate text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ auth()->user()->name }}</p>
                                <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ auth()->user()->email }}</p>
                            </div>
                            <flux:menu.separator />
                            <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                            <flux:menu.separator />
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                    {{ __('Log Out') }}
                                </flux:menu.item>
                            </form>
                        </flux:menu>
                    </flux:dropdown>
                @else
                    <div class="hidden sm:block">
                        <a href="{{ route('login') }}" class="btn-secondary">{{ __('Staff Login') }}</a>
                    </div>
                @endauth
            </flux:header>

            <flux:sidebar stashable sticky class="border-e border-zinc-200 bg-white lg:hidden dark:border-zinc-800 dark:bg-zinc-950">
                <div class="flex items-center justify-between">
                    <x-app-logo />
                    <flux:sidebar.toggle icon="x-mark" />
                </div>

                <flux:navlist variant="outline" class="mt-5">
                    @auth
                        @if(auth()->user()->is_admin)
                            <flux:navlist.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                        @endif
                    @endauth
                    <flux:navlist.item icon="book-open" :href="route('library.index')" :current="request()->routeIs('library.*')" wire:navigate>{{ __('Library') }}</flux:navlist.item>
                    @auth
                        @if(auth()->user()->is_admin)
                            <flux:navlist.item icon="tag" :href="route('admin.taxonomy.index')" :current="request()->routeIs('admin.taxonomy.*')" wire:navigate>{{ __('Taxonomy') }}</flux:navlist.item>
                            <flux:navlist.item icon="chart-bar" :href="route('admin.stats')" :current="request()->routeIs('admin.stats*')" wire:navigate>{{ __('Statistics') }}</flux:navlist.item>
                        @endif
                    @endauth
                </flux:navlist>

                <flux:spacer />

                <div class="border-t border-zinc-200 pt-4 dark:border-zinc-800">
                    <x-language-switcher />
                    @auth
                        <a href="{{ route('settings.profile') }}" class="mt-4 block text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Settings') }}</a>
                        <form method="POST" action="{{ route('logout') }}" class="mt-3">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Log Out') }}</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn-secondary mt-4 w-full">{{ __('Staff Login') }}</a>
                    @endauth
                </div>
            </flux:sidebar>

            {{ $slot }}
        </div>

        @fluxScripts
        @stack('scripts')
    </body>
</html>
