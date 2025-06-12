<x-layouts.app.header :title="$title ?? null">
    <flux:main class="px-2 py-4 sm:px-4 sm:py-8">
        {{ $slot }}
    </flux:main>
</x-layouts.app.header>
