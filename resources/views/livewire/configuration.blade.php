<div x-data="{ showPassword: false }">
    <section class="mt-5">
        <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-200"><i
                        class="fa fa-gears me-4"></i>{{ __('Feeder Configuration') }}</h1>
            </div>
            <div class="mt-5">
                <div class="overflow-x-auto">
                    {{-- form input with url, username and password --}}
                    <form wire:submit.prevent="storePrompt">
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label for="url"
                                    class="block text-sm font-medium text-neutral-900 dark:text-neutral-200">{{
                                    __('URL') }}</label>
                                <input wire:model.live.debounce.300ms="url" type="text" id="url" class="mt-1 block
                                w-full px-3 py-2 text-sm text-neutral-900 placeholder-neutral-300 border border-neutral-300 rounded-lg
                                focus:outline-none focus:border-cyan-600 dark:text-neutral-200 dark:placeholder-neutral-700
                                dark:border-neutral-700 dark:bg-neutral-800" placeholder="{{ __('URL') }}">
                                @error('url') <span class="text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="username"
                                    class="block text-sm font-medium text-neutral-900 dark:text-neutral-200">{{
                                    __('Username') }}</label>
                                <input wire:model.live.debounce.300ms="username" type="text" id="username" class="mt-1 block
                                w-full px-3 py-2 text-sm text-neutral-900 placeholder-neutral-300 border border-neutral-300 rounded-lg
                                focus:outline-none focus:border-cyan-600 dark:text-neutral-200 dark:placeholder-neutral-700
                                dark:border-neutral-700 dark:bg-neutral-800" placeholder="{{ __('Username') }}">
                                @error('username') <span class="text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="password"
                                    class="block text-sm font-medium text-neutral-900 dark:text-neutral-200">{{
                                    __('Password') }}</label>
                                <div class="relative">
                                    <input wire:model.live.debounce.300ms="password" :type="showPassword ? 'text' : 'password'" id="password" class="mt-1 block
                                    w-full px-3 py-2 text-sm text-neutral-900 placeholder-neutral-300 border border-neutral-300 rounded-lg
                                    focus:outline-none focus:border-cyan-600 dark:text-neutral-200 dark:placeholder-neutral-700
                                    dark:border-neutral-700 dark:bg-neutral-800" placeholder="{{ __('Password') }}">
                                    <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5" @click="showPassword = !showPassword">
                                        <i :class="showPassword ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
                                    </button>
                                </div>
                                @error('password') <span class="text-red-500">{{ $message }}</span> @enderror
                            </div>
                            {{-- button save with tosca color in left that 1/4 size of width screen --}}
                            <div class="flex justify-start mt-4">
                                <button type="submit"
                                    class="px-4 py-2 text-sm font-medium bg-cyan-600 border border-transparent rounded-lg
                                hover:bg-cyan-700 focus:outline-none focus:border-cyan-700 focus:ring focus:ring-cyan-200 dark:text-neutral-200
                                dark:bg-cyan-700 dark:hover:bg-cyan-800 dark:focus:border-cyan-700 dark:focus:ring-cyan-200">
                                    {{ __('Simpan') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
    </section>
</div>
