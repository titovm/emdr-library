@props(['item'])

<div class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 hover:scale-105 h-full">
    <div class="p-6 h-full flex flex-col">
        <!-- Title and Date -->
        <div class="mb-4">
            <h3 class="font-bold text-lg text-gray-900 dark:text-gray-100 mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors duration-200">
                {{ $item->title }}
            </h3>
            
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="flex items-center gap-4 mb-4">
                <x-file-count-badge :count="$documentCount" type="document" />
                <x-file-count-badge :count="$videoCount" type="video" />
            </div>
        @endif
        
        <!-- Description -->
        <p class="text-gray-600 dark:text-gray-300 mb-4 text-sm leading-relaxed">
            {{ Str::limit($item->description, 120) }}
        </p>
        
        <!-- Categories -->
        <div class="flex flex-wrap gap-2 mb-6">
            @foreach($item->categories as $category)
                <span class="px-3 py-1 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 rounded-full text-xs font-medium">
                    {{ $category }}
                </span>
            @endforeach
        </div>
        
        <!-- Actions -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700 mt-auto">
            <a href="{{ route('library.show', $item->id) }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium text-sm flex items-center group">
                {{ __('View Details') }}
                <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
            
            <!-- Admin actions -->
            <x-library-admin-actions :item="$item" />
        </div>
    </div>
</div>
