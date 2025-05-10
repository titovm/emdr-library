<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>EMDR Library</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 min-h-screen flex flex-col items-center justify-center">
        <div class="w-full max-w-5xl py-12 px-6">
            <div class="flex flex-col items-center justify-center space-y-8">
                <!-- Logo -->
                <div class="w-64 h-64 flex items-center justify-center">
                    <img src="{{ asset('storage/logo.png') }}" alt="EMDR Library Logo" class="max-w-full max-h-full">
                </div>
                
                <!-- Title -->
                <h1 class="text-4xl font-bold text-center">Welcome to the EMDR Library</h1>
                
                <!-- Description -->
                <p class="text-xl text-center max-w-2xl">
                    A comprehensive collection of resources for EMDR therapists.
                </p>
                
                <!-- Login Button (without Register) -->
                <div class="mt-6">
                    @if (Route::has('login'))
                        <div class="text-center">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition-colors">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition-colors">
                                    Log In
                                </a>
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="w-full py-6 text-center text-gray-600 dark:text-gray-400">
            <p>&copy; {{ date('Y') }} EMDR Library. All rights reserved.</p>
        </footer>
    </body>
</html>
