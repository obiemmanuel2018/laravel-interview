<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class createsuperuser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'register:super-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'register super user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        parent::__construct();
        $this->user = $user;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $details = $this->getDetails();
        $admin = $this->user->createSuperAdmin($details);
        $this->display($admin);

        
        
    }


    private function display(User $admin) : void
    {
        $headers = [ 'Email','Username','Role'];

        $fields = [
           
            'email' => $admin->email,
            'username'=>$admin->user_name,
            'role' => $admin->user_role
        ];

        $this->info('Super User created successfully');
        $this->table($headers, [$fields]);
    }



    private function getDetails() : array
    {
       
        $details['email'] = $this->ask('Email');
        $details['user_name'] = $this->ask('Username');
        $details['password'] = $this->secret('Password');
        $details['confirm_password'] = $this->secret('Confirm password');

        while (! $this->isValidPassword($details['password'], $details['confirm_password'])) {
            if (! $this->isRequiredLength($details['password'])) {
                $this->error('Password must be more that six characters');
            }

            if (! $this->isMatch($details['password'], $details['confirm_password'])) {
                $this->error('Password and Confirm password do not match');
            }

            $details['password'] = $this->secret('Password');
            $details['confirm_password'] = $this->secret('Confirm password');
        }

        return $details;
    }



    private function isValidPassword(string $password, string $confirmPassword) : bool
    {
        return $this->isRequiredLength($password) &&
        $this->isMatch($password, $confirmPassword);
    }

    /**
     * Check if password and confirm password matches.
     *
     * @param string $password
     * @param string $confirmPassword
     * @return bool
     */
    private function isMatch(string $password, string $confirmPassword) : bool
    {
        return $password === $confirmPassword;
    }



     /**
     * Checks if password is longer than six characters.
     *
     * @param string $password
     * @return bool
     */
    private function isRequiredLength(string $password) : bool
    {
        return strlen($password) > 6;
    }
}
