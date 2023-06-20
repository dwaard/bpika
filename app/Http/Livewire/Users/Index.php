<?php

namespace App\Http\Livewire\Users;

use App\Mail\UserInvited;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Index extends Component
{
    use AuthorizesRequests;

    public bool $invitingUser = false;

    /**
     * The name property
     *
     * @var string
     */
    public string $name = '';

    /**
     * The email address property
     *
     * @var string
     */
    public string $email = '';

    /**
     * Validation rules
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3'],
            'email' => ['required', 'email', 'ends_with:@hz.nl', 'unique:users,email'],
        ];
    }

    /**
     * Custom validation error messages
     *
     * @var string[]
     */
    protected array $messages = [
        'email.required' => 'The Email Address cannot be empty.',
        'email.ends_with' => 'The Email Address should be a HZ address.',
        'email.unique' => 'The Email Address is already associated with an account.',
    ];

    public $listeners = ['usersUpdated' => 'render'];

    public function triggerUserInviteModal()
    {
        $this->invitingUser = true;
    }

    /**
     * Invite a new user
     *
     * @return void
     */
    public function inviteNewUser()
    {
        $this->validate();

        Mail::to($this->email)->send(new UserInvited([
            'email' => $this->email,
            'name' => $this->name
        ]));

        $this->invitingUser = false;
        $this->reset(['name', 'email']);
    }

    public function render()
    {
        return view('livewire.users.index', [
          'users' => User::all()
        ]);
    }
}
