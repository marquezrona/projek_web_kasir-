<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@minimarket.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
        );

        User::updateOrCreate(
            ['email' => 'kasir@minimarket.test'],
            [
                'name' => 'Kasir 1',
                'password' => Hash::make('password'),
                'role' => 'kasir',
                'email_verified_at' => now(),
            ],
        );
    }
}
