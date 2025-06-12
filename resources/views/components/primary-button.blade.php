@props(['type' => 'submit'])

<button {{ $attributes->merge(['type' => $type, 'class' => 'inline-flex items-center px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 border border-transparent rounded-lg font-semibold text-sm text-white tracking-wide hover:from-primary-600 hover:to-primary-700 focus:from-primary-600 focus:to-primary-700 active:from-primary-700 active:to-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200 ease-in-out shadow-lg hover:shadow-xl transform hover:scale-105 active:scale-95']) }}>
    {{ $slot }}
</button>