<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl mx-auto">
                    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
                        
                        <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900 dark:text-white">
                            {{ __('Access the Therapist Library') }}
                        </h2>
                    </div>

                    <p class="my-2 text-sm font-medium text-gray-800 dark:text-gray-300">
                        {{ __('Warning! Access to the library is intended for your personal use only. By receiving it, you accept the non-disclosure agreement. In case of its publication in open sources, we will be forced to terminate this service.') }}
                    </p>

                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Please enter your name and email address to access the library resources.') }}
                    </p>

                    <!-- Session Status -->
                    @if (session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('library.process-access') }}" class="mt-6 space-y-6">
                        @csrf

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Consent Checkbox -->
                        <div class="mt-4">
                            <label for="consent" class="inline-flex items-center">
                                <input id="consent" type="checkbox" name="consent" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" required>
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('I agree that my data will be stored for statistical purposes. This data will only be used internally to improve the library services.') }}
                                </span>
                            </label>
                            <x-input-error :messages="$errors->get('consent')" class="mt-2" />
                        </div>
                        
                        <!-- Non-Disclosure Agreement Checkbox -->
                        <div class="mt-4">
                            <label for="nda_consent" class="inline-flex items-center">
                                <input id="nda_consent" type="checkbox" name="nda_consent" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" required>
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('Yes, I agree to the non-disclosure terms') }}
                                </span>
                            </label>
                            <x-input-error :messages="$errors->get('nda_consent')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-3">
                                {{ __('Access Library') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>