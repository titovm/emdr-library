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

                        <!-- Item Type -->
                        <div>
                            <x-input-label :value="__('Item Type')" />
                            <div class="mt-2 space-y-2">
                                <div class="flex items-center">
                                    <input id="type_document" name="type" type="radio" value="document" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ old('type') == 'document' ? 'checked' : '' }} required>
                                    <label for="type_document" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('Document (PDF, Office documents)') }}
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input id="type_video" name="type" type="radio" value="video" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ old('type') == 'video' ? 'checked' : '' }}>
                                    <label for="type_video" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('Video (YouTube, Vimeo, etc.)') }}
                                    </label>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Document File Upload (shown only for document type) -->
                        <div id="document_upload" class="{{ old('type') != 'document' ? 'hidden' : '' }}">
                            <x-input-label for="file" :value="__('Document File')" />
                            <input id="file" name="file" type="file" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-800">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Accepted formats: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX. Max size: 10MB.') }}
                            </p>
                            <x-input-error :messages="$errors->get('file')" class="mt-2" />
                        </div>

                        <!-- External URL (shown only for video type) -->
                        <div id="external_url_input" class="{{ old('type') != 'video' ? 'hidden' : '' }}">
                            <x-input-label for="external_url" :value="__('Video URL')" />
                            <x-text-input id="external_url" name="external_url" type="url" class="mt-1 block w-full" :value="old('external_url')" placeholder="https://www.youtube.com/watch?v=..." />
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
                                    <option value="Therapy Techniques">Therapy Techniques</option>
                                    <option value="Research">Research</option>
                                    <option value="Case Studies">Case Studies</option>
                                    <option value="Training Materials">Training Materials</option>
                                    <option value="Client Resources">Client Resources</option>
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
                                    <option value="EMDR">EMDR</option>
                                    <option value="Trauma">Trauma</option>
                                    <option value="Depression">Depression</option>
                                    <option value="Anxiety">Anxiety</option>
                                    <option value="CBT">CBT</option>
                                    <option value="Children">Children</option>
                                    <option value="Adults">Adults</option>
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

            // Using jQuery for form submission to better debug the issue
            const form = $('form[action="{{ route('library.store') }}"]');
            const submitButton = form.find('button[type="submit"]');
            
            // Add explicit type="submit" to the button if it doesn't have it
            if (!submitButton.attr('type')) {
                submitButton.attr('type', 'submit');
            }
            
            form.on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission
                
                console.log('Form submission intercepted');
                
                // Check CSRF token
                const csrfToken = $('input[name="_token"]').val();
                console.log('CSRF Token exists:', !!csrfToken);
                
                // Check form validity
                const isValid = this.checkValidity();
                console.log('Form is valid:', isValid);
                
                if (!isValid) {
                    console.log('Form validation errors detected');
                    // Let the browser handle the validation
                    return false;
                }
                
                // Log form data being submitted
                const formData = new FormData(this);
                console.log('Form data:');
                for (let pair of formData.entries()) {
                    console.log(pair[0], pair[1]);
                }
                
                // Submit the form using jQuery Ajax
                $.ajax({
                    url: form.attr('action'),
                    method: form.attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        console.log('Sending form data to server...');
                        submitButton.prop('disabled', true).html('Saving...');
                    },
                    success: function(response) {
                        console.log('Form submitted successfully', response);
                        // Redirect to the library index
                        window.location.href = "{{ route('library.index') }}";
                    },
                    error: function(xhr, status, error) {
                        console.error('Form submission error:', error);
                        console.error('Response:', xhr.responseText);
                        
                        // Display error message on the form
                        let errorMessage = 'An error occurred while saving the item.';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMessage = response.message;
                            } else if (response.error) {
                                errorMessage = response.error;
                            }
                        } catch (e) {
                            // If response isn't JSON, use the full text
                            errorMessage = xhr.responseText || errorMessage;
                        }
                        
                        // Create an error alert at the top of the form
                        const errorAlert = $('<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">' +
                            '<strong class="font-bold">Error!</strong>' +
                            '<span class="block sm:inline"> ' + errorMessage + '</span>' +
                            '</div>');
                        
                        form.prepend(errorAlert);
                        
                        // Scroll to the top of the form
                        $('html, body').animate({
                            scrollTop: form.offset().top - 100
                        }, 500);
                    },
                    complete: function() {
                        submitButton.prop('disabled', false).html('Save Item');
                    }
                });
                
                return false; // Prevent default form submission
            });
            
            // Set an initial active radio button if none is selected
            if (!$('input[name="type"]:checked').length) {
                $('#type_document').prop('checked', true).trigger('change');
            }
        });
    </script>
    @endpush
</x-app-layout>