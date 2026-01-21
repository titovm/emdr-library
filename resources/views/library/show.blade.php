<x-app-layout>
    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Modern Card Container -->
            <div class="form-container">
                <!-- Header Section -->
                <header class="mb-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <a href="{{ route('library.index') }}" class="btn-secondary inline-flex items-center justify-center sm:justify-start text-sm py-1.5 px-3">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            <span class="hidden sm:inline">{{ __('Back to Library') }}</span>
                            <span class="sm:hidden">{{ __('Back') }}</span>
                        </a>
                        
                        @if(auth()->check() && auth()->user()->is_admin)
                            <div class="flex items-center gap-2">
                                <a href="{{ route('library.edit', $item->id) }}" class="btn-secondary inline-flex items-center flex-1 sm:flex-none justify-center text-sm py-1.5 px-3">
                                    <svg class="w-3.5 h-3.5 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <span class="hidden sm:inline">{{ __('Edit') }}</span>
                                </a>
                                
                                <form action="{{ route('library.destroy', $item->id) }}" method="POST" class="flex-1 sm:flex-none" onsubmit="return confirm('{{ __('Are you sure you want to delete this item?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger inline-flex items-center w-full sm:w-auto justify-center text-sm py-1.5 px-3">
                                        <svg class="w-3.5 h-3.5 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        <span class="hidden sm:inline">{{ __('Delete') }}</span>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $item->title }}
                    </h1>
                </header>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-4 lg:space-y-6">
                        <!-- Description Section -->
                        <div class="field-section">
                            <div class="section-header">
                                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                    </svg>
                                    {{ __('Description') }}
                                </h2>
                            </div>
                            <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 leading-relaxed text-sm">
                                {{ $item->description }}
                            </div>
                        </div>

                        <!-- Video Embeds Section -->
                        @php $videoFiles = $item->files->where('type', 'video'); @endphp
                        @if($videoFiles->count() > 0)
                            <div class="field-section">
                                <div class="section-header">
                                    <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ __('app.videos') }} ({{ $videoFiles->count() }})
                                    </h2>
                                </div>
                                <div class="space-y-3 sm:space-y-4">
                                    @foreach($videoFiles as $video)
                                        <div class="bg-gray-50 dark:bg-gray-900/50 p-3 sm:p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                                            <h3 class="font-semibold text-sm sm:text-base mb-2 sm:mb-3 text-gray-900 dark:text-gray-100">{{ $video->name }}</h3>
                                            <div class="relative w-full h-60 sm:h-80 lg:h-96 rounded-lg overflow-hidden">
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
                                                    <iframe src="https://www.youtube.com/embed/{{ $videoId }}" 
                                                        class="absolute top-0 left-0 w-full h-full rounded-lg shadow-lg"
                                                        frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                        allowfullscreen></iframe>
                                                @elseif(Str::contains($video->external_url, 'vimeo.com'))
                                                    @php
                                                        $videoId = '';
                                                        if (preg_match('/vimeo\.com\/([0-9]+)/', $video->external_url, $matches)) {
                                                            $videoId = $matches[1];
                                                        }
                                                    @endphp
                                                    <iframe src="https://player.vimeo.com/video/{{ $videoId }}" 
                                                        class="absolute top-0 left-0 w-full h-full rounded-lg shadow-lg"
                                                        frameborder="0" allow="autoplay; fullscreen; picture-in-picture" 
                                                        allowfullscreen></iframe>
                                                @else
                                                    <div class="flex items-center justify-center h-48 sm:h-64 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-lg">
                                                        <a href="{{ $video->external_url }}" target="_blank" rel="noopener noreferrer" 
                                                           class="btn-primary inline-flex items-center text-sm py-1.5 px-3">
                                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                            </svg>
                                                            <span class="hidden sm:inline">{{ __('app.view') }} {{ $video->name }}</span>
                                                            <span class="sm:hidden">{{ __('app.view') }}</span>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Documents Section -->
                        @php $documentFiles = $item->files->where('type', 'document'); @endphp
                        @if($documentFiles->count() > 0)
                            <div class="field-section">
                                <div class="section-header">
                                    <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        {{ __('app.files') }} ({{ $documentFiles->count() }})
                                    </h2>
                                </div>
                                <div class="space-y-3">
                                    @foreach($documentFiles as $file)
                                        <div class="dynamic-input-group">
                                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                                <div class="flex items-center space-x-3 min-w-0 flex-1">
                                                    <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg flex-shrink-0">
                                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm truncate">{{ $file->name }}</h3>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ strtoupper(pathinfo($file->original_filename, PATHINFO_EXTENSION)) }} • 
                                                            {{ $file->formatted_file_size }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <a href="{{ route('library.file.download', $file->id) }}" 
                                                   class="btn-primary text-sm py-1.5 px-3 inline-flex items-center justify-center w-full sm:w-auto flex-shrink-0">
                                                    <svg class="w-3.5 h-3.5 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    <span class="hidden sm:inline">{{ __('app.download') }}</span>
                                                    <span class="sm:hidden">{{ __('Download') }}</span>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($item->files->count() === 0)
                            <div class="field-section text-center py-8">
                                <svg class="w-8 h-8 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400 font-medium text-sm">{{ __('app.no_files') }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('No documents or videos have been uploaded for this item') }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1 space-y-4">
                        <!-- Item Details -->
                        <div class="field-section">
                            <div class="section-header">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ __('Item Details') }}
                                </h3>
                            </div>
                            <div class="space-y-2">
                                @php 
                                    $documentCount = $item->files->where('type', 'document')->count();
                                    $videoCount = $item->files->where('type', 'video')->count();
                                @endphp
                                
                                <div class="flex items-center justify-between p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="font-medium text-sm text-blue-700 dark:text-blue-300">{{ __('app.documents') }}</span>
                                    </div>
                                    <span class="text-blue-600 dark:text-blue-400 font-bold text-sm">{{ $documentCount }}</span>
                                </div>

                                <div class="flex items-center justify-between p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="font-medium text-sm text-purple-700 dark:text-purple-300">{{ __('app.videos') }}</span>
                                    </div>
                                    <span class="text-purple-600 dark:text-purple-400 font-bold text-sm">{{ $videoCount }}</span>
                                </div>

                                <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-3.5 h-3.5 text-gray-600 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('app.created_at') }}</span>
                                    </div>
                                    <span class="text-gray-600 dark:text-gray-400 font-medium text-sm">{{ $item->created_at->format('M d, Y') }}</span>
                                </div>

                                @if($item->updated_at->ne($item->created_at))
                                    <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                        <div class="flex items-center">
                                            <svg class="w-3.5 h-3.5 text-gray-600 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            <span class="font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('app.updated_at') }}</span>
                                        </div>
                                        <span class="text-gray-600 dark:text-gray-400 font-medium text-sm">{{ $item->updated_at->format('M d, Y') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Categories -->
                        @if(count($item->categories) > 0)
                            <div class="field-section">
                                <div class="section-header">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                        {{ __('Categories') }}
                                    </h3>
                                </div>
                                <div class="space-y-2">
                                    @foreach($item->categories as $category)
                                        <a href="{{ route('library.category', $category) }}" 
                                           class="flex items-center p-2 bg-primary-50 dark:bg-primary-900/20 rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900/30 transition-colors duration-200 group">
                                            <svg class="w-3 h-3 text-primary-600 dark:text-primary-400 mr-2 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                            <span class="font-medium text-sm text-primary-700 dark:text-primary-300">{{ $category }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Tags -->
                        @if(count($item->tags) > 0)
                            <div class="field-section">
                                <div class="section-header">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                        {{ __('Tags') }}
                                    </h3>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($item->tags as $tag)
                                        <a href="{{ route('library.tag', $tag) }}" 
                                           class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-xs font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                                            #{{ $tag }}
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
</x-app-layout>