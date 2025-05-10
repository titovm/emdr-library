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

                        <!-- Item Type -->
                        <div>
                            <x-input-label :value="__('Item Type')" />
                            <div class="mt-2 space-y-2">
                                <div class="flex items-center">
                                    <input id="type_document" name="type" type="radio" value="document" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ old('type', $item->type) == 'document' ? 'checked' : '' }} required>
                                    <label for="type_document" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('Document (PDF, Office documents)') }}
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="type_video" name="type" type="radio" value="video" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ old('type', $item->type) == 'video' ? 'checked' : '' }}>
                                    <label for="type_video" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('Video (YouTube, Vimeo, etc.)') }}
                                    </label>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Document File Upload (shown only for document type) -->
                        <div id="document_upload" class="{{ old('type', $item->type) != 'document' ? 'hidden' : '' }}">
                            <x-input-label for="file" :value="__('Document File')" />
                            
                            @if($item->type == 'document' && $item->file_path)
                                <div class="mb-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                                    <p class="text-sm">
                                        {{ __('Current file:') }} 
                                        <span class="font-semibold">{{ basename($item->file_path) }}</span>
                                    </p>
                                </div>
                            @endif
                            
                            <input id="file" name="file" type="file" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-800">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Leave empty to keep the current file. Accepted formats: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX. Max size: 10MB.') }}
                            </p>
                            <x-input-error :messages="$errors->get('file')" class="mt-2" />
                        </div>

                        <!-- External URL (shown only for video type) -->
                        <div id="external_url_input" class="{{ old('type', $item->type) != 'video' ? 'hidden' : '' }}">
                            <x-input-label for="external_url" :value="__('Video URL')" />
                            <x-text-input id="external_url" name="external_url" type="url" class="mt-1 block w-full" :value="old('external_url', $item->external_url)" placeholder="https://www.youtube.com/watch?v=..." />
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Enter the URL for the video (YouTube, Vimeo, etc.).') }}
                            </p>
                            <x-input-error :messages="$errors->get('external_url')" class="mt-2" />
                        </div>

                        <!-- Categories -->
                        <div>
                            <x-input-label for="categories" :value="__('Categories')" />
                            <div class="mt-1">
                                <select id="categories" name="categories[]" class="js-example-basic-multiple w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" multiple="multiple">
                                    <option value="Therapy Techniques" {{ in_array('Therapy Techniques', old('categories', $item->categories ?? [])) ? 'selected' : '' }}>Therapy Techniques</option>
                                    <option value="Research" {{ in_array('Research', old('categories', $item->categories ?? [])) ? 'selected' : '' }}>Research</option>
                                    <option value="Case Studies" {{ in_array('Case Studies', old('categories', $item->categories ?? [])) ? 'selected' : '' }}>Case Studies</option>
                                    <option value="Training Materials" {{ in_array('Training Materials', old('categories', $item->categories ?? [])) ? 'selected' : '' }}>Training Materials</option>
                                    <option value="Client Resources" {{ in_array('Client Resources', old('categories', $item->categories ?? [])) ? 'selected' : '' }}>Client Resources</option>
                                    
                                    @foreach(old('categories', $item->categories ?? []) as $category)
                                        @if(!in_array($category, ['Therapy Techniques', 'Research', 'Case Studies', 'Training Materials', 'Client Resources']))
                                            <option value="{{ $category }}" selected>{{ $category }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('You can select multiple categories or type to create a new one.') }}
                            </p>
                            <x-input-error :messages="$errors->get('categories')" class="mt-2" />
                        </div>

                        <!-- Tags -->
                        <div>
                            <x-input-label for="tags" :value="__('Tags')" />
                            <div class="mt-1">
                                <select id="tags" name="tags[]" class="js-example-basic-multiple w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm" multiple="multiple">
                                    <option value="EMDR" {{ in_array('EMDR', old('tags', $item->tags ?? [])) ? 'selected' : '' }}>EMDR</option>
                                    <option value="Trauma" {{ in_array('Trauma', old('tags', $item->tags ?? [])) ? 'selected' : '' }}>Trauma</option>
                                    <option value="Depression" {{ in_array('Depression', old('tags', $item->tags ?? [])) ? 'selected' : '' }}>Depression</option>
                                    <option value="Anxiety" {{ in_array('Anxiety', old('tags', $item->tags ?? [])) ? 'selected' : '' }}>Anxiety</option>
                                    <option value="CBT" {{ in_array('CBT', old('tags', $item->tags ?? [])) ? 'selected' : '' }}>CBT</option>
                                    <option value="Children" {{ in_array('Children', old('tags', $item->tags ?? [])) ? 'selected' : '' }}>Children</option>
                                    <option value="Adults" {{ in_array('Adults', old('tags', $item->tags ?? [])) ? 'selected' : '' }}>Adults</option>
                                    
                                    @foreach(old('tags', $item->tags ?? []) as $tag)
                                        @if(!in_array($tag, ['EMDR', 'Trauma', 'Depression', 'Anxiety', 'CBT', 'Children', 'Adults']))
                                            <option value="{{ $tag }}" selected>{{ $tag }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('You can select multiple tags or type to create a new one.') }}
                            </p>
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
            // Initialize Select2 for multi-select inputs
            $('.js-example-basic-multiple').select2({
                tags: true,
                tokenSeparators: [','],
                placeholder: 'Select or type to add...'
            });

            // Toggle visibility of file upload and external URL based on item type
            const typeRadios = document.querySelectorAll('input[name="type"]');
            const documentUpload = document.getElementById('document_upload');
            const externalUrlInput = document.getElementById('external_url_input');

            typeRadios.forEach(function(radio) {
                radio.addEventListener('change', function() {
                    if (this.value === 'document') {
                        documentUpload.classList.remove('hidden');
                        externalUrlInput.classList.add('hidden');
                    } else {
                        documentUpload.classList.add('hidden');
                        externalUrlInput.classList.remove('hidden');
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>