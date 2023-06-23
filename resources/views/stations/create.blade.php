<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Stations') }} > {{ __('Create') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Create a new station') }}
                            </h2>
                        </header>

                        <form method="post" action="{{ route('stations.store') }}" class="mt-6 space-y-6">
                            @csrf

                            <div>
                                <x-input-label for="code" :value="__('Code')"></x-input-label>
                                <x-text-input id="code" name="code" type="text" class="mt-1 block w-full"
                                              :value="old('code')" autofocus autocomplete="code"></x-text-input>
                                <x-input-error class="mt-2" :messages="$errors->get('code')"></x-input-error>
                            </div>

                            <div>
                                <x-input-label for="name" :value="__('Name')"></x-input-label>
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                              :value="old('name')" autocomplete="name"></x-text-input>
                                <x-input-error class="mt-2" :messages="$errors->get('name')"></x-input-error>
                            </div>

                            <div>
                                <x-input-label for="city" :value="__('City')"></x-input-label>
                                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full"
                                              :value="old('city')" autocomplete="city"></x-text-input>
                                <x-input-error class="mt-2" :messages="$errors->get('city')"></x-input-error>
                            </div>

                            <div>
                                <x-input-label for="chart_color" :value="__('Chart color')"></x-input-label>
                                <x-text-input id="chart_color" name="chart_color" type="color" class="mt-1 block w-full"
                                              :value="old('chart_color')" autocomplete="chart_color"></x-text-input>
                                <x-input-error class="mt-2" :messages="$errors->get('chart_color')"></x-input-error>
                            </div>

                            <div>
                                <x-input-label for="timezone" :value="__('Timezone')"></x-input-label>
                                <x-timezone-select id="timezone" name="timezone" class="mt-1 block w-full"
                                              :value="old('timezone')"></x-timezone-select>
                                <x-input-error class="mt-2" :messages="$errors->get('timezone')"></x-input-error>
                            </div>

                            <div>
                                <x-input-label for="latitude" :value="__('Latitude')"></x-input-label>
                                <x-text-input id="latitude" name="latitude" type="text" class="mt-1 block w-full"
                                              :value="old('latitude')" autocomplete="latitude">
                                </x-text-input>
                                <x-input-error class="mt-2" :messages="$errors->get('latitude')"></x-input-error>
                            </div>

                            <div>
                                <x-input-label for="longitude" :value="__('Longitude')"></x-input-label>
                                <x-text-input id="longitude" name="longitude" type="text" class="mt-1 block w-full"
                                              :value="old('longitude')" autocomplete="longitude">
                                </x-text-input>
                                <x-input-error class="mt-2" :messages="$errors->get('longitude')"></x-input-error>
                            </div>


                            <div class="flex items-center gap-4">
                                <x-primary-button type="submit">{{ __('Save') }}</x-primary-button>
                                <x-secondary-button onclick="location.href='{{ url()->previous() }}'">{{ __('Cancel') }}</x-secondary-button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
