<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Edit Library Item') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Update the information for this library item.') }}
                        </p>
                    </header>

                    <form method="POST" action="{{ route('library.update', $item->id) }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Title -->
                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $item->title)" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" rows="4">{{ old('description', $item->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Existing Files -->
                        @if($item->files && $item->files->count() > 0)
                        <div>
                            <x-input-label :value="__('Current Files')" />
                            <div class="mt-2 space-y-2">
                                @foreach($item->files as $file)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                                    <div class="flex items-center space-x-3">
                                        @if($file->type === 'document')
                                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $file->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ ucfirst($file->type) }}
                                                @if($file->type === 'document' && $file->formatted_file_size)
                                                    • {{ $file->formatted_file_size }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if($file->type === 'document')
                                            <a href="{{ route('library.file.download', $file->id) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">{{ __('Download') }}</a>
                                        @else
                                            <a href="{{ $file->external_url }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-sm">{{ __('View') }}</a>
                                        @endif
                                        <label class="flex items-center">
                                            <input type="checkbox" name="delete_files[]" value="{{ $file->id }}" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                                            <span class="ml-1 text-xs text-red-600">{{ __('app.remove') }}</span>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Add New Document Files -->
                        <div id="files_section">
                            <div class="flex items-center justify-between">
                                <x-input-label :value="__('Add New Documents')" />
                                <button type="button" id="add_file_btn" class="px-3 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                    {{ __('app.add_files') }}
                                </button>
                            </div>
                            <div id="files_container" class="mt-2 space-y-3">
                                <!-- File inputs will be added here dynamically -->
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Accepted formats: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX. Max size: 10MB per file.') }}
                            </p>
                            <x-input-error :messages="$errors->get('files')" class="mt-2" />
                            <x-input-error :messages="$errors->get('files.*')" class="mt-2" />
                        </div>

                        <!-- Add New Video URLs -->
                        <div id="videos_section">
                            <div class="flex items-center justify-between">
                                <x-input-label :value="__('Add New Videos')" />
                                <button type="button" id="add_video_btn" class="px-3 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                    {{ __('app.add_videos') }}
                                </button>
                            </div>
                            <div id="videos_container" class="mt-2 space-y-3">
                                <!-- Video inputs will be added here dynamically -->
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Enter URLs for videos (YouTube, Vimeo, etc.).') }}
                            </p>
                            <x-input-error :messages="$errors->get('videos')" class="mt-2" />
                            <x-input-error :messages="$errors->get('videos.*')" class="mt-2" />
                        </div>

                        <!-- Categories -->
                        <div>
                            <taxonomy-selector 
                                id="categories"
                                label="{{ __('Categories') }}"
                                name="categories"
                                :initial-options='@json($categories)'
                                :initial-selected='@json(old("categories", $item->categories ?? []))'
                                :multiple="false"
                                :is-required="true"
                                required-message="{{ __('Please select a category') }}"
                                placeholder="{{ __('Select a category...') }}"
                                help-text="{{ __('Select a category or type to create a new one.') }}">
                            </taxonomy-selector>
                            <x-input-error :messages="$errors->get('categories')" class="mt-2" />
                        </div>

                        <!-- Tags -->
                        <div>
                            <taxonomy-selector 
                                id="tags"
                                label="{{ __('Tags') }}"
                                name="tags"
                                :initial-options='@json($tags)'
                                :initial-selected='@json(old("tags", $item->tags ?? []))'
                                help-text="{{ __('You can select multiple tags or type to create a new one.') }}">
                            </taxonomy-selector>
                            <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                        </div>

                        <!-- Publish Status -->
                        <div class="block">
                            <label for="is_published" class="flex items-center">
                                <input id="is_published" name="is_published" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" value="1" {{ old('is_published', $item->is_published) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Publish this item (visible in the library)') }}</span>
                            </label>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Update Item') }}</x-primary-button>
                            
                            <a href="{{ route('library.show', $item->id) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let fileIndex = 0;
            let videoIndex = 0;

            // Add file input functionality
            document.getElementById('add_file_btn').addEventListener('click', function() {
                const container = document.getElementById('files_container');
                const fileDiv = document.createElement('div');
                fileDiv.className = 'flex items-center space-x-3';
                fileDiv.innerHTML = `
                    <input type="file" name="files[]" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx" 
                           class="flex-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <button type="button" class="remove-file-btn px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                        {{ __('app.remove') }}
                    </button>
                `;
                container.appendChild(fileDiv);

                // Add remove functionality
                fileDiv.querySelector('.remove-file-btn').addEventListener('click', function() {
                    fileDiv.remove();
                });
            });

            // Add video input functionality  
            document.getElementById('add_video_btn').addEventListener('click', function() {
                const container = document.getElementById('videos_container');
                const videoDiv = document.createElement('div');
                videoDiv.className = 'flex items-center space-x-3';
                videoDiv.innerHTML = `
                    <input type="url" name="videos[]" placeholder="{{ __('Enter video URL...') }}"
                           class="flex-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <button type="button" class="remove-video-btn px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                        {{ __('app.remove') }}
                    </button>
                `;
                container.appendChild(videoDiv);

                // Add remove functionality
                videoDiv.querySelector('.remove-video-btn').addEventListener('click', function() {
                    videoDiv.remove();
                });
            });
        });
    </script>
    @endpush
</x-app-layout>