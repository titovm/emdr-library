<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Modern Card Container -->
            <div class="form-container">
                <!-- Header Section -->
                <header class="form-header">
                    <h1 class="form-title">
                        📚 {{ __('Add New Library Item') }}
                    </h1>
                    <p class="form-subtitle">
                        {{ __('Add a new document or video to the therapist library.') }}
                    </p>
                </header>

                <form method="POST" action="{{ route('library.store') }}" class="space-y-8" enctype="multipart/form-data">
                    @csrf

                    <!-- Basic Information Section -->
                    <div class="field-section">
                        <div class="section-header">
                            <h3 class="section-title">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ __('Basic Information') }}
                            </h3>
                        </div>

                        <!-- Title -->
                        <div class="form-field">
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="form-field">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="mt-1 block w-full border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 focus:border-primary-500 dark:focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 rounded-lg px-4 py-3 transition-all duration-200" rows="4" placeholder="Enter a detailed description of the library item...">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Document Files Section -->
                    <div class="field-section" id="files_section">
                        <div class="section-header">
                            <h3 class="section-title">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                {{ __('app.documents') }}
                            </h3>
                            <button type="button" id="add_file_btn" class="btn-secondary">
                                <svg class="icon inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                {{ __('app.add_files') }}
                            </button>
                        </div>

                        <div id="files_container" class="space-y-4">
                            <!-- File inputs will be added here dynamically -->
                        </div>

                        <div class="help-text">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ __('Accepted formats: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX. Max size: 10MB per file.') }}
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('files')" class="mt-2" />
                        <x-input-error :messages="$errors->get('files.*')" class="mt-2" />
                    </div>

                    <!-- video URLs Section -->
                    <div class="field-section" id="videos_section">
                        <div class="section-header">
                            <h3 class="section-title">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                {{ __('app.videos') }}
                            </h3>
                            <button type="button" id="add_video_btn" class="btn-secondary">
                                <svg class="icon inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                {{ __('app.add_videos') }}
                            </button>
                        </div>

                        <div id="videos_container" class="space-y-4">
                            <!-- Video inputs will be added here dynamically -->
                        </div>

                        <div class="help-text">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ __('Enter URLs for videos (YouTube, Vimeo, etc.).') }}
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('videos')" class="mt-2" />
                        <x-input-error :messages="$errors->get('videos.*')" class="mt-2" />
                    </div>

                    <!-- Categories and Tags Section -->
                    <div class="field-section">
                        <div class="section-header">
                            <h3 class="section-title">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                {{ __('Categorization') }}
                            </h3>
                        </div>

                        <div id="vue-app" class="grid md:grid-cols-2 gap-6">
                            <!-- Categories -->
                            <div class="form-field">
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
                            <div class="form-field">
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
                        </div>
                    </div>

                    <!-- Publishing Options Section -->
                    <div class="field-section">
                        <div class="section-header">
                            <h3 class="section-title">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                                {{ __('Publishing Options') }}
                            </h3>
                        </div>

                        <!-- Publish Status -->
                        <div class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                            <input id="is_published" name="is_published" type="checkbox" 
                                   class="h-5 w-5 text-primary-600 focus:ring-primary-500 border-gray-300 rounded" 
                                   value="1" checked>
                            <label for="is_published" class="ml-3 flex items-center cursor-pointer">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('Publish this item (will be immediately visible in the library)') }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Section -->
                    <div class="flex items-center justify-center pt-6 border-t border-gray-200 dark:border-gray-700">
                        <x-primary-button class="btn-primary">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Save Item') }}
                        </x-primary-button>
                    </div>
                </form>
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
                    fileDiv.className = 'dynamic-input-group';
                    fileDiv.innerHTML = `
                        <div class="flex items-center space-x-3">
                            <div class="flex-1">
                                <input type="file" name="files[]" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx" 
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 file:cursor-pointer border-2 border-gray-200 dark:border-gray-600 rounded-lg focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 bg-white dark:bg-gray-800">
                            </div>
                            <button type="button" class="btn-danger remove-file-btn flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Remove
                            </button>
                        </div>
                    `;
                    filesContainer.appendChild(fileDiv);

                    // Add remove functionality
                    fileDiv.querySelector('.remove-file-btn').addEventListener('click', function() {
                        fileDiv.style.animation = 'fadeOut 0.3s ease-out';
                        setTimeout(() => fileDiv.remove(), 300);
                    });

                    // Add fade in animation
                    fileDiv.style.animation = 'fadeIn 0.3s ease-in';
                });
            }

            // Add video input functionality
            const addVideoBtn = document.getElementById('add_video_btn');
            const videosContainer = document.getElementById('videos_container');

            if (addVideoBtn && videosContainer) {
                addVideoBtn.addEventListener('click', function() {
                    const videoDiv = document.createElement('div');
                    videoDiv.className = 'dynamic-input-group';
                    videoDiv.innerHTML = `
                        <div class="flex items-center space-x-3">
                            <div class="flex-1">
                                <input type="url" name="videos[]" placeholder="https://youtube.com/watch?v=... or https://vimeo.com/..."
                                       class="block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500">
                            </div>
                            <button type="button" class="btn-danger remove-video-btn flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Remove
                            </button>
                        </div>
                    `;
                    videosContainer.appendChild(videoDiv);

                    // Add remove functionality
                    videoDiv.querySelector('.remove-video-btn').addEventListener('click', function() {
                        videoDiv.style.animation = 'fadeOut 0.3s ease-out';
                        setTimeout(() => videoDiv.remove(), 300);
                    });

                    // Add fade in animation
                    videoDiv.style.animation = 'fadeIn 0.3s ease-in';
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

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }

        /* File input styling improvements */
        input[type="file"]::-webkit-file-upload-button {
            background: linear-gradient(135deg, #65b136, #529429);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        input[type="file"]::-webkit-file-upload-button:hover {
            background: linear-gradient(135deg, #529429, #477a23);
            transform: translateY(-1px);
        }

        /* Focus styles for better accessibility */
        input:focus, textarea:focus, select:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(101, 177, 54, 0.1);
        }

        /* Smooth transitions for all interactive elements */
        * {
            transition: all 0.2s ease;
        }
    </style>
    @endpush
</x-app-layout>