<x-app-layout>
    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
            <!-- Modern Card Container -->
            <div class="form-container">
                <!-- Header Section -->
                <header class="form-header mb-4">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        @if(auth()->check() && auth()->user()->is_admin)
                            🛠️ {{ __('Manage Library') }}
                        @else
                            📚 {{ __('EMDR Therapist Library') }}
                        @endif
                    </h1>
                </header>

                <!-- Action Bar -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-3 p-3 bg-white dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
                    @if(auth()->check() && auth()->user()->is_admin)
                        <a href="{{ route('library.create') }}" class="btn-primary inline-flex items-center text-sm py-2 px-4">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            {{ __('Add New Item') }}
                        </a>
                    @else
                        <div></div>
                    @endif
                    
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('library.index') }}" class="flex items-center gap-2">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="{{ __('Search...') }}"
                            class="w-32 sm:w-auto px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:border-primary-500 focus:ring-1 focus:ring-primary-500/20 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                        />
                        <button type="submit" class="btn-primary inline-flex items-center whitespace-nowrap text-sm py-1.5 px-4">
                            <svg class="w-4 h-4 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span class="hidden sm:inline">{{ __('Search') }}</span>
                        </button>
                        @if(request()->filled('search'))
                            <a href="{{ route('library.index') }}" class="btn-secondary inline-flex items-center whitespace-nowrap text-sm py-1.5 px-3">
                                <svg class="w-4 h-4 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span class="hidden sm:inline">{{ __('Reset') }}</span>
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Session Status Messages -->
                @if (session('error'))
                    <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-xs font-medium text-red-700 dark:text-red-300">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-xs font-medium text-green-700 dark:text-green-300">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                    <!-- Sidebar with Filters -->
                    <div class="lg:col-span-1">
                        <!-- Categories Filter -->
                        <div class="field-section mb-4">
                            <div class="section-header">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    {{ __('Categories') }}
                                </h3>
                            </div>
                            <ul class="space-y-1">
                                @foreach($categories as $category)
                                    <li>
                                        <a href="{{ route('library.index', array_filter(['category' => $category, 'search' => request('search')])) }}" 
                                           class="flex items-center p-2 rounded-lg transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm {{ isset($activeCategory) && $activeCategory === $category ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">
                                            <svg class="w-3 h-3 mr-1.5 {{ isset($activeCategory) && $activeCategory === $category ? 'text-primary-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                            {{ $category }}
                                        </a>
                                    </li>
                                @endforeach
                                
                                @if(isset($activeCategory) || isset($activeTag))
                                    <li class="pt-2 mt-2 border-t border-gray-200 dark:border-gray-700">
                                        <a href="{{ route('library.index', request()->filled('search') ? ['search' => request('search')] : []) }}" 
                                           class="flex items-center p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-200 text-sm">
                                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    {{ __('Tags') }}
                                </h3>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($tags as $tag)
                                    <a href="{{ route('library.index', array_filter(['category' => $activeCategory ?? null, 'tag' => $tag, 'search' => request('search')])) }}" 
                                       class="px-2 py-1 rounded text-xs font-medium transition-all duration-200 {{ isset($activeTag) && $activeTag === $tag ? 'bg-primary-500 text-white shadow-lg' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
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
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                                @foreach($items as $item)
                                    <x-library-item-card :item="$item" />
                                @endforeach
                            </div>
                            
                            <!-- Pagination -->
                            <div class="mt-6 flex justify-center">
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-4 py-3 border border-gray-200 dark:border-gray-700">
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