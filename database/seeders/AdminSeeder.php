<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed the default Admin account.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'fullname'   => 'System Administrator',
                'username'   => 'admin',
                'ic_number'  => '000000000000',
                'email'      => 'admin@gmail.com',
                'password'   => Hash::make('password'),
                'role'       => 'admin',
                'status'     => 'active',
            ]
        );

        $this->command->info('Admin account created: username=admin / password=password');
    }
}
