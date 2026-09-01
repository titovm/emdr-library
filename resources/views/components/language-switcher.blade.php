<div class="flex items-center gap-1 text-xs" aria-label="{{ __('Language') }}">
    <a href="{{ route('language.switch', ['locale' => 'en']) }}" class="rounded px-2 py-1 font-semibold {{ app()->getLocale() === 'en' ? 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' }}">EN</a>
    <a href="{{ route('language.switch', ['locale' => 'ru']) }}" class="rounded px-2 py-1 font-semibold {{ app()->getLocale() === 'ru' ? 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-950' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100' }}">RU</a>
</div>
