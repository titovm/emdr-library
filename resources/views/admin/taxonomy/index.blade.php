<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-900 dark:text-green-300" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-900 dark:text-red-300" role="alert">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="md:flex md:space-x-6">
                <!-- Categories Section -->
                <div class="md:w-1/2 p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg mb-6 md:mb-0">
                    <div class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Categories Management') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Add, edit, or remove categories for library items.') }}
                            </p>
                        </header>

                        <!-- Add New Category Form -->
                        <form method="POST" action="{{ route('admin.taxonomy.categories.store') }}" class="mt-6">
                            @csrf
                            <div class="flex">
                                <x-text-input id="category_name" name="name" type="text" class="mt-1 block w-full mr-2" placeholder="New Category Name" required />
                                <x-primary-button>{{ __('Add') }}</x-primary-button>
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </form>

                        <!-- Categories List -->
                        <div class="mt-6">
                            <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Existing Categories') }}</h3>
                            
                            @if(count($categories) > 0)
                                <div class="space-y-4">
                                    @foreach($categories as $category)
                                        <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                            <span class="text-gray-800 dark:text-gray-200">{{ $category }}</span>
                                            
                                            <div class="flex space-x-2">
                                                <!-- Edit Button - Opens modal -->
                                                <button onclick="openEditCategoryModal('{{ $category }}')" class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                    </svg>
                                                </button>
                                                
                                                <!-- Delete Form -->
                                                <form method="POST" action="{{ route('admin.taxonomy.categories.destroy') }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this category? It will be removed from all library items.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="name" value="{{ $category }}">
                                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-400">{{ __('No categories found. Add your first category above.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tags Section -->
                <div class="md:w-1/2 p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Tags Management') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Add, edit, or remove tags for library items.') }}
                            </p>
                        </header>

                        <!-- Add New Tag Form -->
                        <form method="POST" action="{{ route('admin.taxonomy.tags.store') }}" class="mt-6">
                            @csrf
                            <div class="flex">
                                <x-text-input id="tag_name" name="name" type="text" class="mt-1 block w-full mr-2" placeholder="New Tag Name" required />
                                <x-primary-button>{{ __('Add') }}</x-primary-button>
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </form>

                        <!-- Tags List -->
                        <div class="mt-6">
                            <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Existing Tags') }}</h3>
                            
                            @if(count($tags) > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($tags as $tag)
                                        <div class="group relative inline-flex items-center px-3 py-1 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md text-sm">
                                            {{ $tag }}
                                            
                                            <div class="hidden group-hover:flex absolute right-0 top-0 -mt-2 -mr-2 space-x-1">
                                                <!-- Edit Button - Opens modal -->
                                                <button onclick="openEditTagModal('{{ $tag }}')" class="bg-yellow-500 text-white rounded-full p-1 hover:bg-yellow-600">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                    </svg>
                                                </button>
                                                
                                                <!-- Delete Form -->
                                                <form method="POST" action="{{ route('admin.taxonomy.tags.destroy') }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this tag? It will be removed from all library items.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="name" value="{{ $tag }}">
                                                    <button type="submit" class="bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-400">{{ __('No tags found. Add your first tag above.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editCategoryModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Edit Category') }}
                            </h3>
                            
                            <form method="POST" action="{{ route('admin.taxonomy.categories.update') }}" class="mt-4">
                                @csrf
                                @method('PUT')
                                <input type="hidden" id="edit_category_old_name" name="old_name" value="">
                                
                                <div class="mb-4">
                                    <x-input-label for="edit_category_new_name" :value="__('New Category Name')" />
                                    <x-text-input id="edit_category_new_name" name="new_name" type="text" class="mt-1 block w-full" required />
                                    <x-input-error :messages="$errors->get('new_name')" class="mt-2" />
                                </div>
                                
                                <div class="flex justify-end">
                                    <button type="button" onclick="closeEditCategoryModal()" class="bg-gray-400 dark:bg-gray-600 text-white px-4 py-2 rounded-md mr-2 hover:bg-gray-500 dark:hover:bg-gray-700">
                                        {{ __('Cancel') }}
                                    </button>
                                    <x-primary-button>{{ __('Save') }}</x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Tag Modal -->
    <div id="editTagModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Edit Tag') }}
                            </h3>
                            
                            <form method="POST" action="{{ route('admin.taxonomy.tags.update') }}" class="mt-4">
                                @csrf
                                @method('PUT')
                                <input type="hidden" id="edit_tag_old_name" name="old_name" value="">
                                
                                <div class="mb-4">
                                    <x-input-label for="edit_tag_new_name" :value="__('New Tag Name')" />
                                    <x-text-input id="edit_tag_new_name" name="new_name" type="text" class="mt-1 block w-full" required />
                                    <x-input-error :messages="$errors->get('new_name')" class="mt-2" />
                                </div>
                                
                                <div class="flex justify-end">
                                    <button type="button" onclick="closeEditTagModal()" class="bg-gray-400 dark:bg-gray-600 text-white px-4 py-2 rounded-md mr-2 hover:bg-gray-500 dark:hover:bg-gray-700">
                                        {{ __('Cancel') }}
                                    </button>
                                    <x-primary-button>{{ __('Save') }}</x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Category Modal Functions
        function openEditCategoryModal(categoryName) {
            document.getElementById('edit_category_old_name').value = categoryName;
            document.getElementById('edit_category_new_name').value = categoryName;
            document.getElementById('editCategoryModal').classList.remove('hidden');
        }
        
        function closeEditCategoryModal() {
            document.getElementById('editCategoryModal').classList.add('hidden');
        }
        
        // Tag Modal Functions
        function openEditTagModal(tagName) {
            document.getElementById('edit_tag_old_name').value = tagName;
            document.getElementById('edit_tag_new_name').value = tagName;
            document.getElementById('editTagModal').classList.remove('hidden');
        }
        
        function closeEditTagModal() {
            document.getElementById('editTagModal').classList.add('hidden');
        }
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            const categoryModal = document.getElementById('editCategoryModal');
            const tagModal = document.getElementById('editTagModal');
            
            if (event.target === categoryModal) {
                closeEditCategoryModal();
            }
            
            if (event.target === tagModal) {
                closeEditTagModal();
            }
        }
    </script>
    @endpush
</x-app-layout>