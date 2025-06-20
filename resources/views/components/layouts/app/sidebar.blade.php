<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <div id="app"> <!-- Added id="app" here for Vue to mount -->
            <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

                <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                    <x-app-logo />
                </a>

                @auth
                    @if(auth()->user()->is_admin)
                    <!-- Admin Navigation -->
                    <flux:navlist variant="outline">
                        <flux:navlist.group :heading="__('Admin Panel')" class="grid">
                            <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                            <flux:navlist.item icon="book-open" :href="route('library.index')" :current="request()->routeIs('library.*')">{{ __('Manage Library') }}</flux:navlist.item>
                        </flux:navlist.group>
                    </flux:navlist>
                    @else
                    <!-- Regular User Navigation -->
                    <flux:navlist variant="outline">
                        <flux:navlist.group :heading="__('Library')" class="grid">
                            <flux:navlist.item icon="book-open" :href="route('library.index')" :current="request()->routeIs('library.*')">{{ __('Browse Library') }}</flux:navlist.item>
                        </flux:navlist.group>
                    </flux:navlist>
                    @endif
                @else
                    <!-- Guest Navigation - Show library access form link -->
                    <flux:navlist variant="outline">
                        <flux:navlist.group :heading="__('Library Access')" class="grid">
                            <flux:navlist.item icon="book-open" :href="route('library.access')" :current="request()->routeIs('library.access')">{{ __('Access Library') }}</flux:navlist.item>
                        </flux:navlist.group>
                    </flux:navlist>
                @endauth

                <flux:spacer />

                <!-- Only show user menu for authenticated users -->
                @auth
                    <!-- Desktop User Menu -->
                    <flux:dropdown position="bottom" align="start">
                        <flux:profile
                            :name="auth()->user()->name"
                            :initials="auth()->user()->initials()"
                            icon-trailing="chevrons-up-down"
                        />

                        <flux:menu class="w-[220px]">
                            <flux:menu.radio.group>
                                <div class="p-0 text-sm font-normal">
                                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                        <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                            <span
                                                class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                            >
                                                {{ auth()->user()->initials() }}
                                            </span>
                                        </span>

                                        <div class="grid flex-1 text-start text-sm leading-tight">
                                            <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                            <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </flux:menu.radio.group>

                            <flux:menu.separator />

                            <flux:menu.radio.group>
                                <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                            </flux:menu.radio.group>

                            <flux:menu.separator />

                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                    {{ __('Log Out') }}
                                </flux:menu.item>
                            </form>
                        </flux:menu>
                    </flux:dropdown>

                    <!-- Mobile User Menu -->
                    <flux:header class="lg:hidden">
                        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

                        <flux:spacer />

                        <flux:dropdown position="top" align="end">
                            <flux:profile
                                :initials="auth()->user()->initials()"
                                icon-trailing="chevron-down"
                            />

                            <flux:menu>
                                <flux:menu.radio.group>
                                    <div class="p-0 text-sm font-normal">
                                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                                <span
                                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                                >
                                                    {{ auth()->user()->initials() }}
                                                </span>
                                            </span>

                                            <div class="grid flex-1 text-start text-sm leading-tight">
                                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </flux:menu.radio.group>

                                <flux:menu.separator />

                                <flux:menu.radio.group>
                                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                                </flux:menu.radio.group>

                                <flux:menu.separator />

                                <form method="POST" action="{{ route('logout') }}" class="w-full">
                                    @csrf
                                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                        {{ __('Log Out') }}
                                    </flux:menu.item>
                                </form>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:header>
                @else
                    <!-- Link to login for guests -->
                    <flux:navlist variant="outline">
                        <flux:navlist.item icon="arrow-right" :href="route('login')">{{ __('Login') }}</flux:navlist.item>
                    </flux:navlist>
                @endauth

            </flux:sidebar>

            {{ $slot }}
        </div>

        @fluxScripts
        
        <!-- Page Scripts (moved from head to end of body) -->
        @stack('scripts')
    </body>
</html>
