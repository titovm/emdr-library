<div {{ $attributes->merge(['class' => 'flex min-w-0 items-center gap-2.5']) }}>
    <div class="flex h-8 w-10 shrink-0 items-center justify-center border border-primary-700 bg-primary-700 text-white dark:border-primary-400 dark:bg-primary-400 dark:text-primary-950" style="border-radius: 6px;">
        <x-app-logo-icon class="h-5 w-7 fill-current" />
    </div>
    <div class="min-w-0 leading-tight">
        <span class="block truncate text-sm font-semibold tracking-tight text-zinc-950 dark:text-zinc-50">EMDR Library</span>
        <span class="hidden truncate text-[11px] text-zinc-500 sm:block dark:text-zinc-400">{{ __('Professional resources') }}</span>
    </div>
</div>
