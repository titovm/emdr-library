<x-app-layout>
    <div class="py-4 sm:py-8">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
            <!-- Modern Card Container -->
            <div class="form-container">
                <!-- Header Section -->
                <header class="form-header">
                    <h1 class="form-title">
                        @if(auth()->check() && auth()->user()->is_admin)
                            🛠️ {{ __('Manage Library') }}
                        @else
                            📚 {{ __('EMDR Therapist Library') }}
                        @endif
                    </h1>
                    <p class="form-subtitle">
                        @if(auth()->check() && auth()->user()->is_admin)
                            {{ __('Manage and organize your EMDR therapy resources') }}
                        @else
                            {{ __('Discover and access professional EMDR therapy resources') }}
                        @endif
                    </p>
                </header>

                <!-- Action Bar -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 sm:mb-8 gap-4 p-3 sm:p-6 bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700">
                    @if(auth()->check() && auth()->user()->is_admin)
                        <a href="{{ route('library.create') }}" class="btn-primary inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            {{ __('Add New Item') }}
                        </a>
                    @else
                        <div></div>
                    @endif
                    
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('library.index') }}" class="flex items-center gap-3">
                        <div class="relative">
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="{{ __('Search library...') }}"
                                class="w-32 sm:w-auto pl-10 pr-4 py-2 border-2 border-gray-200 dark:border-gray-600 rounded-lg focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 transition-all duration-200"
                            />
                            <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <button type="submit" class="btn-primary inline-flex items-center whitespace-nowrap">
                            <svg class="w-4 h-4 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span class="hidden sm:inline">{{ __('Search') }}</span>
                        </button>
                        @if(request()->filled('search'))
                            <a href="{{ route('library.index') }}" class="btn-secondary inline-flex items-center whitespace-nowrap">
                                <svg class="w-4 h-4 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span class="hidden sm:inline">{{ __('Reset') }}</span>
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Session Status Messages -->
                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-red-700 dark:text-red-300">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-green-700 dark:text-green-300">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 sm:gap-8">
                    <!-- Sidebar with Filters -->
                    <div class="lg:col-span-1">
                        <!-- Categories Filter -->
                        <div class="field-section mb-6">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    {{ __('Categories') }}
                                </h3>
                            </div>
                            <ul class="space-y-3">
                                @foreach($categories as $category)
                                    <li>
                                        <a href="{{ route('library.category', $category) }}" 
                                           class="flex items-center p-3 rounded-lg transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-700 {{ isset($activeCategory) && $activeCategory === $category ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">
                                            <svg class="w-4 h-4 mr-2 {{ isset($activeCategory) && $activeCategory === $category ? 'text-primary-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                            {{ $category }}
                                        </a>
                                    </li>
                                @endforeach
                                
                                @if(isset($activeCategory) || isset($activeTag))
                                    <li class="pt-3 border-t border-gray-200 dark:border-gray-700">
                                        <a href="{{ route('library.index') }}" 
                                           class="flex items-center p-3 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            {{ __('Reset filters') }}
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>

                        <!-- Tags Filter -->
                        <div class="field-section">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    {{ __('Tags') }}
                                </h3>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($tags as $tag)
                                    <a href="{{ route('library.tag', $tag) }}" 
                                       class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ isset($activeTag) && $activeTag === $tag ? 'bg-primary-500 text-white shadow-lg' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                                        #{{ $tag }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Library Items Grid -->
                    <div class="lg:col-span-3">
                        @if($items->isEmpty())
                            <div class="field-section text-center py-16">
                                <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-xl font-medium text-gray-500 dark:text-gray-400 mb-2">{{ __('No items found') }}</p>
                                <p class="text-gray-400 dark:text-gray-500">{{ __('Try adjusting your search or filters') }}</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                                @foreach($items as $item)
                                    <x-library-item-card :item="$item" />
                                @endforeach
                            </div>
                            
                            <!-- Pagination -->
                            <div class="mt-8 flex justify-center">
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg px-6 py-4 border border-gray-200 dark:border-gray-700">
                                    {{ $items->appends(request()->only('search'))->links() }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>