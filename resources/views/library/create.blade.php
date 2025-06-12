<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Add New Library Item') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Add a new document or video to the therapist library.') }}
                        </p>
                    </header>

                    <form method="POST" action="{{ route('library.store') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                        @csrf

                        <!-- Title -->
                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" rows="4">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Document Files -->
                        <div id="files_section">
                            <div class="flex items-center justify-between">
                                <x-input-label :value="__('app.documents')" />
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

                        <!-- Video URLs -->
                        <div id="videos_section">
                            <div class="flex items-center justify-between">
                                <x-input-label :value="__('app.videos')" />
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
                                :initial-selected='@json(old("categories", []))'
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
                                :initial-selected='@json(old("tags", []))'
                                help-text="{{ __('You can select multiple tags or type to create a new one.') }}">
                            </taxonomy-selector>
                            <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                        </div>

                        <!-- Publish Status -->
                        <div class="block">
                            <label for="is_published" class="flex items-center">
                                <input id="is_published" name="is_published" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" value="1" checked>
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Publish this item (will be immediately visible in the library)') }}</span>
                            </label>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Save Item') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add file input functionality
            const addFileBtn = document.getElementById('add_file_btn');
            const filesContainer = document.getElementById('files_container');

            if (addFileBtn && filesContainer) {
                addFileBtn.addEventListener('click', function() {
                    const fileDiv = document.createElement('div');
                    fileDiv.className = 'flex items-center space-x-3';
                    fileDiv.innerHTML = `
                        <input type="file" name="files[]" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx" 
                               class="flex-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <button type="button" class="remove-file-btn px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                            Remove
                        </button>
                    `;
                    filesContainer.appendChild(fileDiv);

                // Add remove functionality
                fileDiv.querySelector('.remove-file-btn').addEventListener('click', function() {
                    fileDiv.remove();
                });
            });
            }

            // Add video input functionality
            const addVideoBtn = document.getElementById('add_video_btn');
            const videosContainer = document.getElementById('videos_container');

            if (addVideoBtn && videosContainer) {
                addVideoBtn.addEventListener('click', function() {
                    const videoDiv = document.createElement('div');
                    videoDiv.className = 'flex items-center space-x-3';
                    videoDiv.innerHTML = `
                        <input type="url" name="videos[]" placeholder="Enter video URL..."
                               class="flex-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <button type="button" class="remove-video-btn px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                            Remove
                        </button>
                    `;
                    videosContainer.appendChild(videoDiv);

                    // Add remove functionality
                    videoDiv.querySelector('.remove-video-btn').addEventListener('click', function() {
                        videoDiv.remove();
                    });
                });
            }

            // Add initial file and video inputs if there are old values
            @if(old('files'))
                @for($i = 0; $i < count(old('files')); $i++)
                    if (addFileBtn) addFileBtn.click();
                @endfor
            @endif

            @if(old('videos'))
                const oldVideos = @json(old('videos'));
                oldVideos.forEach(function(videoUrl) {
                    if (addVideoBtn) addVideoBtn.click();
                    const lastVideoInput = videosContainer.lastElementChild.querySelector('input[type="url"]');
                    if (lastVideoInput) {
                        lastVideoInput.value = videoUrl;
                    }
                });
            @endif
        });
    </script>
    @endpush
</x-app-layout>