<?php

namespace App\Http\Livewire\Users;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ListItem extends Component
{

    public User $user;

    /**
     * Indicates if user deletion is being confirmed.
     *
     * @var bool
     */
    public $confirmingUserDeletion = false;

    public function render()
    {
        return view('livewire.users.list-item');
    }

    /**
     * Confirm that the user would like to delete their account.
     *
     * @return void
     */
    public function confirmUserDeletion()
    {
        $this->resetErrorBag();

        $this->password = '';

        $this->dispatchBrowserEvent('confirming-delete-user');

        $this->confirmingUserDeletion = true;
    }

    /**
     * Delete the current user.
     *
     */
    public function deleteUser()
    {
        $this->resetErrorBag();

        $this->user->delete();
        $this->emit('usersUpdated');
        $this->confirmingUserDeletion = false;
    }


}
