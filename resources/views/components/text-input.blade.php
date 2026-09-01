@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'w-full rounded-md border border-zinc-300 bg-white px-3 py-2.5 text-base text-zinc-950 placeholder:text-zinc-500 focus:border-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-600/20 disabled:cursor-not-allowed disabled:opacity-60 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:placeholder:text-zinc-500 dark:focus:border-primary-400 dark:focus:ring-primary-400/20']) !!}>
