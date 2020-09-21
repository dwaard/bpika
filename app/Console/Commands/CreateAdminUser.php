<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = $this->getUserData();
        $validator = Validator::make(
            $data,
            $this->getValidationRules()
        );
        if ($validator->fails()) {
            $this->info(__('Admin User not created. See error messages below:'));
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }
        $user = $this->createUser($data);
        $this->info(__("Admin user :username is created", [ 'username' => $user->name]));
    }

    /**
     * Get the data from the user
     *
     * @return array
     */
    protected function getUserData(): array
    {
        return [
            'name' => $this->ask(__('Name')),
            'email' => $this->ask(__('E-Mail Address')),
            'password' => $this->secret(__('Password')),
            'password_confirmation' => $this->secret(__('Confirm Password')),
        ];
    }

    /**
     * The rules to validate the user data.
     *
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Create the admin user.
     *
     * @param array $data
     * @return mixed
     */
    protected function createUser(array $data): User
    {
        // Hash the password before creation
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        // Do all the stuff here to make the user an admin user
        $user->email_verified_at = now();

        $user->save();
        return $user;
    }

}
