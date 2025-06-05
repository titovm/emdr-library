<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">
                            @if(auth()->check() && auth()->user()->is_admin)
                                {{ __('Manage Library') }}
                            @else
                                {{ __('EMDR Therapist Library') }}
                            @endif
                        </h2>
                        
                        <div class="flex items-center space-x-4">
                            
                            @if(auth()->check() && auth()->user()->is_admin)
                                <a href="{{ route('library.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Add New Item') }}
                                </a>
                            @endif
                            <!-- Search Form -->
                            <form method="GET" action="{{ route('library.index') }}" class="flex items-center space-x-2">
                                <input
                                    type="text"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="{{ __('Search') }}"
                                    class="border rounded-md py-1 px-2 text-sm"
                                />
                                <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded-md">
                                    {{ __('Search') }}
                                </button>
                                @if(request()->filled('search'))
                                    <a href="{{ route('library.index') }}" class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                        {{ __('Reset Search') }}
                                    </a>
                                @endif
                            </form>
                        </div>
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
                                    
                                    @if(isset($activeCategory) || isset($activeTag))
                                        <li class="mt-3">
                                            <a href="{{ route('library.index') }}" 
                                               class="flex items-center text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                {{ __('Reset filters') }}
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>

                            <div>
                                <h3 class="font-semibold text-lg mb-3">{{ __('Tags') }}</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($tags as $tag)
                                        <a href="{{ route('library.tag', $tag) }}" 
                                           class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded-md text-sm hover:bg-gray-300 dark:hover:bg-gray-600 {{ isset($activeTag) && $activeTag === $tag ? 'font-bold bg-blue-200 dark:bg-blue-800' : '' }}">
                                            {{ $tag }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Library Items Grid -->
                        <div class="col-span-1 md:col-span-3">
                            @if($items->isEmpty())
                                <div class="text-center py-8">
                                    <p class="text-gray-500 dark:text-gray-400">{{ __('No items found.') }}</p>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @foreach($items as $item)
                                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-600">
                                            <div class="p-4">
                                                <h3 class="font-semibold text-lg mb-2">{{ $item->title }}</h3>
                                                
                                                <div class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                                                    <span class="inline-flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        {{ $item->created_at->format('M d, Y') }}
                                                    </span>
                                                </div>

                                                <!-- File counts -->
                                                @if($item->files && $item->files->count() > 0)
                                                    @php 
                                                        $documentCount = $item->files->where('type', 'document')->count();
                                                        $videoCount = $item->files->where('type', 'video')->count();
                                                    @endphp
                                                    <div class="flex space-x-4 text-sm text-gray-500 dark:text-gray-400 mb-3">
                                                        @if($documentCount > 0)
                                                            <span class="flex items-center">
                                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                {{ $documentCount }} {{ __('app.documents') }}
                                                            </span>
                                                        @endif
                                                        @if($videoCount > 0)
                                                            <span class="flex items-center">
                                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M2 6a2 2 0 012-2h6l2 2h6a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                                                                    <path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 10l-4-4-6 6h8a1 1 0 001-1z"/>
                                                                </svg>
                                                                {{ $videoCount }} {{ __('app.videos') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                                
                                                <p class="text-gray-600 dark:text-gray-300 mb-4 text-sm">
                                                    {{ Str::limit($item->description, 100) }}
                                                </p>
                                                
                                                <div class="flex flex-wrap gap-1 mb-4">
                                                    @foreach($item->categories as $category)
                                                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-100 rounded-md text-xs">
                                                            {{ $category }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                                
                                                <div class="flex justify-between items-center">
                                                    <a href="{{ route('library.show', $item->id) }}" class="text-blue-600 hover:underline">
                                                        {{ __('View Details') }}
                                                    </a>
                                                    
                                                    <!-- Admin actions -->
                                                    @if(auth()->check() && auth()->user()->is_admin)
                                                        <div class="flex space-x-2">
                                                            <a href="{{ route('library.edit', $item->id) }}" class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                </svg>
                                                            </a>
                                                            
                                                            <form action="{{ route('library.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="mt-6">
                                    {{ $items->appends(request()->only('search'))->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>