@props(['value', 'for' => null])

<label {{ $for ? "for=\"{$for}\"" : '' }} {{ $attributes->merge(['class' => 'block text-sm font-semibold text-zinc-800 dark:text-zinc-200']) }}>
    {{ $value ?? $slot }}
</label>
