<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Stations') }}
        </h2>
    </x-slot>

    <x-index-with-actions>
        <x-slot name="actions">
            <x-primary-button onclick="location.href='{{ route('stations.create') }}';">
                {{ __('Add a new station') }}
            </x-primary-button>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-sm text-gray-500 sm:text-right">
                {{ $stations->count() }} {{ __('Stations') }}
            </div>

        </x-slot>

        @forelse($stations as $station)
            <x-station-list-item :station="$station"></x-station-list-item>
        @empty
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __('No stations available') }}
                </div>
            </div>
        @endforelse
    </x-index-with-actions>

</x-app-layout>
