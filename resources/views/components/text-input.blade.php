@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 focus:border-primary-500 dark:focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 rounded-lg px-4 py-3 transition-all duration-200']) !!}>