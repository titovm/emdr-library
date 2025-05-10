<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <a href="{{ route('admin.stats') }}" class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 hover:border-blue-500 dark:hover:border-blue-400 transition-all duration-200">
                <div class="absolute inset-0 size-full p-6 flex flex-col">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Visitor Statistics</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Track library visitors, popular content, and user activity</p>
                    <div class="flex items-center mt-auto">
                        <span class="text-blue-600 dark:text-blue-400 text-sm font-medium">View Statistics</span>
                        <svg class="w-4 h-4 ml-1 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            </a>
            <a href="{{ route('admin.taxonomy.index') }}" class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 hover:border-blue-500 dark:hover:border-blue-400 transition-all duration-200">
                <div class="absolute inset-0 size-full p-6 flex flex-col">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Categories & Tags</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Manage categories and tags for the library content</p>
                    <div class="flex items-center mt-auto">
                        <span class="text-blue-600 dark:text-blue-400 text-sm font-medium">Manage Taxonomy</span>
                        <svg class="w-4 h-4 ml-1 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            </a>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>
</x-layouts.app>
