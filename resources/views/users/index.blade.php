<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Users') }}
        </h2>
    </x-slot>

    <x-index-with-actions>
        <x-slot name="actions">
            <x-primary-button>
                {{ __('Invite a new user') }}
            </x-primary-button>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-sm text-gray-500 sm:text-right">
                {{ $users->count() }} {{ __('Users') }}
            </div>

        </x-slot>

        @forelse($users as $user)
            <x-user-list-item :user="$user"></x-user-list-item>
        @empty
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __('No users available') }}
                </div>
            </div>
        @endforelse
    </x-index-with-actions>
</x-app-layout>
