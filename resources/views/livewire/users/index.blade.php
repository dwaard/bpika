<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Users') }}
    </h2>
</x-slot>

<x-index-with-actions>
    <x-slot name="actions">
        <x-primary-button wire:click="triggerUserInviteModal()">
            {{ __('Invite a new user') }}
        </x-primary-button>

        <x-dialog-modal wire:model="invitingUser">
            <x-slot name="title">
                {{ __('Invite a new user') }}
            </x-slot>

            <x-slot name="content">
            {{ __('Please provide a valid HZ and unique email address of the user to invite') }}

            <!-- Name -->
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="name" value="{{ __('Name') }}" />
                    <x-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="name" autocomplete="name" />
                    @error('name') <span class="text-sm text-red-600 space-y-1">{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="email" value="{{ __('Email') }}" />
                    <x-input id="email" type="email" class="mt-1 block w-full" wire:model.defer="email" />
                    @error('email') <span class="text-sm text-red-600 space-y-1">{{ $message }}</span> @enderror
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$set('invitingUser', false)" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ml-3" wire:click="inviteNewUser()" wire:loading.attr="disabled">
                    {{ __('Send invitation') }}
                </x-primary-button>
            </x-slot>
        </x-dialog-modal>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-sm text-gray-500 sm:text-right">
            {{ $users->count() }} {{ __('Users') }}
        </div>

    </x-slot>

    @forelse($users as $user)
        @livewire('users.list-item', ['user' => $user], key($user->id))
    @empty
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                {{ __('No users available') }}
            </div>
        </div>
    @endforelse
</x-index-with-actions>
