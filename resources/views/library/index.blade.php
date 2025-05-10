<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">{{ __('Therapist Library') }}</h2>
                        
                        @auth
                            <a href="{{ route('library.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Add New Item') }}
                            </a>
                        @endauth
                    </div>

                    <!-- Session Status -->
                    @if (session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- Sidebar with Filters -->
                        <div class="col-span-1">
                            <div class="mb-6">
                                <h3 class="font-semibold text-lg mb-3">{{ __('Categories') }}</h3>
                                <ul class="space-y-2">
                                    @foreach($categories as $category)
                                        <li>
                                            <a href="{{ route('library.category', $category) }}" 
                                               class="text-blue-600 hover:underline {{ isset($activeCategory) && $activeCategory === $category ? 'font-bold' : '' }}">
                                                {{ $category }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div>
                                <h3 class="font-semibold text-lg mb-3">{{ __('Tags') }}</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($tags as $tag)
                                        <a href="{{ route('library.tag', $tag) }}" 
                                           class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded-md text-sm {{ isset($activeTag) && $activeTag === $tag ? 'bg-blue-500 text-white' : '' }}">
                                            {{ $tag }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Library Items Grid -->
                        <div class="col-span-1 md:col-span-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @forelse($items as $item)
                                    <div class="border dark:border-gray-700 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200">
                                        <div class="p-4">
                                            <h3 class="font-semibold text-lg mb-2">
                                                <a href="{{ route('library.show', $item->id) }}" class="hover:text-blue-600">
                                                    {{ $item->title }}
                                                </a>
                                            </h3>
                                            
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                                {{ Str::limit($item->description, 100) }}
                                            </p>
                                            
                                            <div class="flex justify-between items-center">
                                                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-xs rounded-full">
                                                    {{ $item->type === 'document' ? 'Document' : 'Video' }}
                                                </span>
                                                
                                                <a href="{{ route('library.show', $item->id) }}" class="text-blue-600 text-sm hover:underline">
                                                    {{ __('View Details') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-3 text-center py-8">
                                        <p>{{ __('No library items found.') }}</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="mt-6">
                                {{ $items->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>