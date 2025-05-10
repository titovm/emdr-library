<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-4">
                        <a href="{{ route('library.index') }}" class="text-blue-600 hover:underline">
                            &larr; {{ __('Back to Library') }}
                        </a>
                    </div>

                    <div class="flex flex-col lg:flex-row gap-8">
                        <div class="w-full lg:w-2/3">
                            <h1 class="text-2xl font-bold mb-4">{{ $item->title }}</h1>
                            
                            <div class="prose dark:prose-invert max-w-none mb-6">
                                {{ $item->description }}
                            </div>

                            <div class="mb-6">
                                @if($item->type === 'document')
                                    <a href="{{ route('library.download', $item->id) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Download Document') }}
                                    </a>
                                @else
                                    <div class="aspect-w-16 aspect-h-9">
                                        @if(Str::contains($item->external_url, 'youtube.com') || Str::contains($item->external_url, 'youtu.be'))
                                            @php
                                                // Extract YouTube video ID
                                                $videoId = '';
                                                if (Str::contains($item->external_url, 'youtube.com/watch?v=')) {
                                                    $videoId = explode('v=', $item->external_url)[1];
                                                    $ampersandPosition = strpos($videoId, '&');
                                                    if ($ampersandPosition !== false) {
                                                        $videoId = substr($videoId, 0, $ampersandPosition);
                                                    }
                                                } elseif (Str::contains($item->external_url, 'youtu.be/')) {
                                                    $videoId = explode('youtu.be/', $item->external_url)[1];
                                                }
                                            @endphp
                                            <iframe width="100%" height="400" src="https://www.youtube.com/embed/{{ $videoId }}" 
                                                frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                allowfullscreen class="rounded-lg"></iframe>
                                        @elseif(Str::contains($item->external_url, 'vimeo.com'))
                                            @php
                                                // Extract Vimeo video ID
                                                $videoId = '';
                                                if (preg_match('/vimeo\.com\/([0-9]+)/', $item->external_url, $matches)) {
                                                    $videoId = $matches[1];
                                                }
                                            @endphp
                                            <iframe src="https://player.vimeo.com/video/{{ $videoId }}" 
                                                width="100%" height="400" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" 
                                                allowfullscreen class="rounded-lg"></iframe>
                                        @else
                                            <a href="{{ $item->external_url }}" target="_blank" rel="noopener noreferrer" 
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                {{ __('View External Video') }}
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>

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
                                    <p>
                                        <span class="font-medium">{{ __('Type:') }}</span> 
                                        {{ $item->type === 'document' ? 'Document' : 'Video' }}
                                    </p>
                                    <p>
                                        <span class="font-medium">{{ __('Added:') }}</span> 
                                        {{ $item->created_at->format('M d, Y') }}
                                    </p>
                                    @if($item->updated_at->ne($item->created_at))
                                        <p>
                                            <span class="font-medium">{{ __('Updated:') }}</span> 
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