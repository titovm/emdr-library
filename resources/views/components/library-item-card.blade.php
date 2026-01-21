@props(['item'])

<div class="group bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-200 h-full">
    <div class="p-4 h-full flex flex-col">
        <!-- Title and Date -->
        <div class="mb-3">
            <a href="{{ route('library.show', $item->id) }}">
                <h3 class="font-semibold text-base text-gray-900 dark:text-gray-100 mb-1.5 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors cursor-pointer">
                    {{ $item->title }}
                </h3>
            </a>
            
            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ $item->created_at->format('M d, Y') }}
            </div>
        </div>

        <!-- File counts with modern icons -->
        @if($item->files && $item->files->count() > 0)
            @php 
                $documentCount = $item->files->where('type', 'document')->count();
                $videoCount = $item->files->where('type', 'video')->count();
            @endphp
            <div class="flex items-center gap-3 mb-3">
                <x-file-count-badge :count="$documentCount" type="document" />
                <x-file-count-badge :count="$videoCount" type="video" />
            </div>
        @endif
        
        <!-- Description -->
        <p class="text-gray-600 dark:text-gray-300 mb-3 text-xs leading-relaxed line-clamp-2">
            {{ Str::limit($item->description, 100) }}
        </p>
        
        <!-- Categories -->
        <div class="flex flex-wrap gap-1.5 mb-4">
            @foreach($item->categories as $category)
                <span class="px-2 py-0.5 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 rounded text-xs font-medium">
                    {{ $category }}
                </span>
            @endforeach
        </div>
        
        <!-- Actions -->
        <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-700 mt-auto">
            <a href="{{ route('library.show', $item->id) }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium text-xs flex items-center group">
                {{ __('View Details') }}
                <svg class="w-3.5 h-3.5 ml-1 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
            
            <!-- Admin actions -->
            <x-library-admin-actions :item="$item" />
        </div>
    </div>
</div>
