<x-panel class="hover:bg-gray-50">
    <div class="flex items-center gap-4">
        <div class="grow p-2 w-full">
            {{ $user->name }}<br>
            <span class="text-xs">{{ $user->email }}</span>
        </div>
        <div class="flex-none p-4">
            <x-danger-button wire:click="confirmUserDeletion" wire:loading.attr="disabled">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
            </x-danger-button>

            <!-- Delete User Confirmation Modal -->
            <x-dialog-modal wire:model="confirmingUserDeletion">
                <x-slot name="title">
                    {{ __('Delete Account') }}
                </x-slot>

                <x-slot name="content">
                    {{ __('Are you sure you want to delete this account?') }}
                </x-slot>

                <x-slot name="footer">
                    <x-secondary-button wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-danger-button class="ml-3" wire:click="deleteUser" wire:loading.attr="disabled">
                        {{ __('Delete Account') }}
                    </x-danger-button>
                </x-slot>
            </x-dialog-modal>

        </div>
    </div>
</x-panel>
