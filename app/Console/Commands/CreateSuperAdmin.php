<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdmin extends Command
{
    protected $signature = 'superadmin:create';
    protected $description = 'Create the default superadmin user';

    public function handle()
    {
        $email = 'superadmin@superschool.com'; // or just 'superadmin' if email validation allows
        $name = 'superadmin';
        $password = 'superadmin';

        $user = User::where('name', $name)->orWhere('email', $email)->first();

        if (!$user) {
            $user = new User();
            $user->name = $name;
            $user->email = $email; // use email for unique constrain usually
            $user->password = Hash::make($password);
            $user->role = 'superadmin';
            $user->save();
            $this->info("Superadmin user created. Login ID: $name (or $email), Password: $password");
        } else {
            $user->password = Hash::make($password);
            $user->role = 'superadmin';
            $user->name = $name; // Ensure username match
            $user->save();
            $this->info("Superadmin user updated. Login ID: $name, Password: $password");
        }
    }
}
