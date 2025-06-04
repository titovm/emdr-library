<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('library.index') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-500">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                {{ __('Back to Library') }}
                            </a>
                        </div>
                        
                        @if(auth()->check() && auth()->user()->is_admin)
                            <div class="flex space-x-2">
                                <a href="{{ route('library.edit', $item->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Edit') }}
                                </a>
                                
                                <form action="{{ route('library.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this item?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Delete') }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col lg:flex-row gap-8">
                        <div class="w-full lg:w-2/3">
                            <h1 class="text-2xl font-bold mb-4">{{ $item->title }}</h1>
                            
                            <div class="prose dark:prose-invert max-w-none mb-6">
                                {{ $item->description }}
                            </div>

                            <!-- Video Embeds Section -->
                            @php $videoFiles = $item->files->where('type', 'video'); @endphp
                            @if($videoFiles->count() > 0)
                                <div class="mb-8">
                                    <h2 class="text-xl font-semibold mb-4">{{ __('app.videos') }}</h2>
                                    <div class="space-y-6">
                                        @foreach($videoFiles as $video)
                                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                                <h3 class="font-medium mb-3">{{ $video->name }}</h3>
                                                <div class="aspect-w-16 aspect-h-9">
                                                    @if(Str::contains($video->external_url, 'youtube.com') || Str::contains($video->external_url, 'youtu.be'))
                                                        @php
                                                            $videoId = '';
                                                            if (Str::contains($video->external_url, 'youtube.com/watch?v=')) {
                                                                $videoId = explode('v=', $video->external_url)[1];
                                                                $ampersandPosition = strpos($videoId, '&');
                                                                if ($ampersandPosition !== false) {
                                                                    $videoId = substr($videoId, 0, $ampersandPosition);
                                                                }
                                                            } elseif (Str::contains($video->external_url, 'youtu.be/')) {
                                                                $videoId = explode('youtu.be/', $video->external_url)[1];
                                                            }
                                                        @endphp
                                                        <iframe width="100%" height="400" src="https://www.youtube.com/embed/{{ $videoId }}" 
                                                            frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                            allowfullscreen class="rounded-lg"></iframe>
                                                    @elseif(Str::contains($video->external_url, 'vimeo.com'))
                                                        @php
                                                            $videoId = '';
                                                            if (preg_match('/vimeo\.com\/([0-9]+)/', $video->external_url, $matches)) {
                                                                $videoId = $matches[1];
                                                            }
                                                        @endphp
                                                        <iframe src="https://player.vimeo.com/video/{{ $videoId }}" 
                                                            width="100%" height="400" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" 
                                                            allowfullscreen class="rounded-lg"></iframe>
                                                    @else
                                                        <div class="flex items-center justify-center h-64 bg-gray-100 dark:bg-gray-600 rounded-lg">
                                                            <a href="{{ $video->external_url }}" target="_blank" rel="noopener noreferrer" 
                                                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                                {{ __('app.view') }} {{ $video->name }}
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Files Section -->
                            @php $documentFiles = $item->files->where('type', 'document'); @endphp
                            @if($documentFiles->count() > 0)
                                <div class="mb-8">
                                    <h2 class="text-xl font-semibold mb-4">{{ __('app.files') }}</h2>
                                    <div class="space-y-4">
                                        @foreach($documentFiles as $file)
                                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                                <div class="flex items-center space-x-3">
                                                    <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <div>
                                                        <h3 class="font-medium">{{ $file->name }}</h3>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ strtoupper(pathinfo($file->original_filename, PATHINFO_EXTENSION)) }} • 
                                                            {{ $file->formatted_file_size }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="flex space-x-2">
                                                    <a href="{{ route('library.file.download', $file->id) }}" 
                                                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                        {{ __('app.download') }}
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($item->files->count() === 0)
                                <div class="mb-8 p-6 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                                    <p class="text-gray-500 dark:text-gray-400">{{ __('app.no_files') }}</p>
                                </div>
                            @endif

                            @auth
                                <div class="flex space-x-4 mt-8">
                                    <a href="{{ route('library.edit', $item->id) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                        {{ __('Edit') }}
                                    </a>

                                    <form action="{{ route('library.destroy', $item->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('{{ __('Are you sure you want to delete this item?') }}')" 
                                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </div>
                            @endauth
                        </div>

                        <div class="w-full lg:w-1/3 bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <div class="mb-6">
                                <h3 class="font-semibold text-lg mb-2">{{ __('Item Details') }}</h3>
                                <div class="space-y-2 text-sm">
                                    @php 
                                        $documentCount = $item->files->where('type', 'document')->count();
                                        $videoCount = $item->files->where('type', 'video')->count();
                                    @endphp
                                    <p>
                                        <span class="font-medium">{{ __('app.documents') }}:</span> 
                                        {{ $documentCount }}
                                    </p>
                                    <p>
                                        <span class="font-medium">{{ __('app.videos') }}:</span> 
                                        {{ $videoCount }}
                                    </p>
                                    <p>
                                        <span class="font-medium">{{ __('app.created_at') }}:</span> 
                                        {{ $item->created_at->format('M d, Y') }}
                                    </p>
                                    @if($item->updated_at->ne($item->created_at))
                                        <p>
                                            <span class="font-medium">{{ __('app.updated_at') }}:</span> 
                                            {{ $item->updated_at->format('M d, Y') }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            @if(count($item->categories) > 0)
                                <div class="mb-6">
                                    <h3 class="font-semibold text-lg mb-2">{{ __('Categories') }}</h3>
                                    <div class="space-y-1">
                                        @foreach($item->categories as $category)
                                            <a href="{{ route('library.category', $category) }}" 
                                               class="text-blue-600 hover:underline block">
                                                {{ $category }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(count($item->tags) > 0)
                                <div>
                                    <h3 class="font-semibold text-lg mb-2">{{ __('Tags') }}</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($item->tags as $tag)
                                            <a href="{{ route('library.tag', $tag) }}" 
                                               class="px-2 py-1 bg-gray-200 dark:bg-gray-600 rounded-md text-sm">
                                                {{ $tag }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>