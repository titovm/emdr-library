<div class="flex items-center space-x-2">
    <a href="{{ route('language.switch', ['locale' => 'en']) }}" class="{{ app()->getLocale() == 'en' ? 'font-bold' : 'text-gray-500' }}">EN</a>
    <span class="text-gray-400">|</span>
    <a href="{{ route('language.switch', ['locale' => 'ru']) }}" class="{{ app()->getLocale() == 'ru' ? 'font-bold' : 'text-gray-500' }}">RU</a>
</div>