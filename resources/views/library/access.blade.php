<x-app-layout>
    <main class="py-6 sm:py-10">
        <div class="app-container">
            <div class="mx-auto grid max-w-5xl overflow-hidden border border-zinc-200 bg-white lg:grid-cols-[0.85fr_1.15fr] dark:border-zinc-800 dark:bg-zinc-900" style="border-radius: 8px;">
                <section class="border-b border-zinc-200 bg-zinc-100 p-5 sm:p-7 lg:border-b-0 lg:border-e dark:border-zinc-800 dark:bg-zinc-950">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-primary-700 dark:text-primary-300">{{ __('Restricted professional access') }}</p>
                    <h1 class="mt-2 text-2xl font-semibold tracking-tight text-zinc-950 dark:text-zinc-50">{{ __('Access the Therapist Library') }}</h1>
                    <p class="mt-3 text-sm leading-6 text-zinc-700 dark:text-zinc-300">{{ __('Please enter your name and email address to access the library resources.') }}</p>

                    <div class="mt-6 border-s-2 border-primary-600 ps-3 text-sm leading-6 text-zinc-700 dark:border-primary-400 dark:text-zinc-300">
                        {{ __('Warning! Access to the library is intended for your personal use only. By receiving it, you accept the non-disclosure agreement. In case of its publication in open sources, we will be forced to terminate this service.') }}
                    </div>

                    <dl class="mt-7 space-y-3 text-sm">
                        <div class="flex items-start gap-2.5">
                            <flux:icon.lock-closed class="mt-0.5 size-4 shrink-0 text-primary-700 dark:text-primary-300" />
                            <div>
                                <dt class="font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Personal access') }}</dt>
                                <dd class="text-zinc-600 dark:text-zinc-400">{{ __('Do not share files or access links with third parties.') }}</dd>
                            </div>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <flux:icon.shield-check class="mt-0.5 size-4 shrink-0 text-primary-700 dark:text-primary-300" />
                            <div>
                                <dt class="font-semibold text-zinc-900 dark:text-zinc-100">{{ __('Data processing') }}</dt>
                                <dd class="text-zinc-600 dark:text-zinc-400">{{ __('Your details are used to provide and record library access.') }}</dd>
                            </div>
                        </div>
                    </dl>
                </section>

                <section class="p-5 sm:p-7">
                    @if (session('error'))
                        <div class="mb-4 rounded-md border border-red-300 bg-red-50 px-3 py-2.5 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/50 dark:text-red-200" role="alert">{{ session('error') }}</div>
                    @endif

                    @if (session('success'))
                        <div class="mb-4 rounded-md border border-primary-300 bg-primary-50 px-3 py-2.5 text-sm text-primary-900 dark:border-primary-800 dark:bg-primary-950/50 dark:text-primary-200" role="status">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('library.process-access') }}" class="space-y-5">
                        @csrf

                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1.5" :value="old('name')" autocomplete="name" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1.5" :value="old('email')" autocomplete="email" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                        </div>

                        <div class="space-y-3 border-t border-zinc-200 pt-4 dark:border-zinc-800">
                            <div>
                                <label for="consent" class="flex cursor-pointer items-start gap-3">
                                    <input id="consent" type="checkbox" name="consent" value="1" class="mt-1 size-4 rounded border-zinc-400 text-primary-700 focus:ring-primary-600 dark:border-zinc-600 dark:bg-zinc-950 dark:text-primary-400 dark:focus:ring-primary-400" required>
                                    <span class="text-sm leading-5 text-zinc-700 dark:text-zinc-300">
                                        Даю согласие на обработку моих персональных данных (имя, e-mail) для предоставления доступа к онлайн-библиотеке и сервисных уведомлений.
                                        <a href="{{ route('privacy-consent') }}" class="font-semibold text-primary-700 underline-offset-2 hover:underline dark:text-primary-300">{{ __('Подробнее') }}</a>
                                    </span>
                                </label>
                                <x-input-error :messages="$errors->get('consent')" class="mt-1.5" />
                            </div>

                            <div>
                                <label for="nda_consent" class="flex cursor-pointer items-start gap-3">
                                    <input id="nda_consent" type="checkbox" name="nda_consent" value="1" class="mt-1 size-4 rounded border-zinc-400 text-primary-700 focus:ring-primary-600 dark:border-zinc-600 dark:bg-zinc-950 dark:text-primary-400 dark:focus:ring-primary-400" required>
                                    <span class="text-sm leading-5 text-zinc-700 dark:text-zinc-300">
                                        Я принимаю Условия использования и нераспространения материалов: не буду передавать файлы/ссылки третьим лицам, публиковать материалы или их части (включая скриншоты/записи экрана).
                                        <a href="{{ route('terms') }}" class="font-semibold text-primary-700 underline-offset-2 hover:underline dark:text-primary-300">{{ __('Читать условия') }}</a>
                                    </span>
                                </label>
                                <x-input-error :messages="$errors->get('nda_consent')" class="mt-1.5" />
                            </div>
                        </div>

                        <x-primary-button class="w-full">{{ __('Access Library') }}</x-primary-button>
                    </form>
                </section>
            </div>
        </div>
    </main>
</x-app-layout>
