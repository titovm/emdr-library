<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 text-gray-900 dark:text-gray-100 transition-colors duration-300">
        <div class="min-h-screen flex flex-col">
            <!-- Main Content -->
            <div class="flex-1 flex items-center justify-center py-8 px-4 sm:py-12 sm:px-6 lg:px-8">
                <div class="w-full max-w-4xl">
                    <!-- Modern Card Container -->
                    <div class="form-container text-center">
                        <!-- Logo -->
                        <div class="w-48 h-48 sm:w-64 sm:h-64 mx-auto mb-8 flex items-center justify-center">
                            <img src="{{ asset('logo.png') }}" alt="EMDR Library Logo" class="max-w-full max-h-full rounded-2xl shadow-lg dark:shadow-2xl dark:shadow-gray-900/50 transition-shadow duration-300">
                        </div>
                        
                        <!-- Title -->
                        <div class="form-header">
                            <h1 class="form-title text-3xl sm:text-4xl lg:text-5xl">
                                🧠 {{ __('Welcome to the EMDR Library') }}
                            </h1>
                            <p class="form-subtitle text-lg sm:text-xl max-w-3xl mx-auto mt-4">
                                {{ __('A comprehensive collection of resources for EMDR therapists.') }}
                            </p>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="mt-8 sm:mt-12">
                            @if (Route::has('login'))
                                <div class="space-y-6">
                                    @auth
                                        <div>
                                            <a href="{{ url('/dashboard') }}" class="btn-primary inline-flex items-center text-lg px-8 py-4">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                </svg>
                                                {{ __('Dashboard') }}
                                            </a>
                                        </div>
                                    @else
                                        <div class="space-y-4">
                                            <!-- Primary Access Button -->
                                            <div>
                                                <a href="{{ route('library.index') }}" class="btn-primary inline-flex items-center text-lg px-8 py-4">
                                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                    {{ __('Access Library') }}
                                                </a>
                                            </div>
                                            
                                            <!-- Staff Login Link -->
                                            <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ __('Are you a staff member?') }}</p>
                                                <a href="{{ route('login') }}" class="btn-secondary inline-flex items-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                                    </svg>
                                                    {{ __('Staff Login') }}
                                                </a>
                                            </div>
                                        </div>
                                    @endauth
                                </div>
                            @endif
                        </div>
                        
                        <!-- Features Grid -->
                        <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="field-section text-center">
                                <div class="w-12 h-12 mx-auto mb-4 bg-primary-100 dark:bg-primary-900/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('app.documents') }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Professional EMDR resources and guides') }}</p>
                            </div>
                            
                            <div class="field-section text-center">
                                <div class="w-12 h-12 mx-auto mb-4 bg-primary-100 dark:bg-primary-900/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('app.videos') }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Training videos and demonstrations') }}</p>
                            </div>
                            
                            <div class="field-section text-center sm:col-span-2 lg:col-span-1">
                                <div class="w-12 h-12 mx-auto mb-4 bg-primary-100 dark:bg-primary-900/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ __('Organized') }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Categorized and tagged for easy access') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <footer class="py-6 border-t border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-gray-900/50 backdrop-blur-sm transition-colors duration-300">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300">
                        &copy; {{ date('Y') }} EMDR Library. {{ __('All rights reserved') }}.
                    </p>
                </div>
            </footer>
        </div>
    </body>
</html>
