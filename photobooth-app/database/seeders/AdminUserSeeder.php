<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'username' => 'AdminA',
                'password' => Hash::make('1234'),
                'is_admin' => true,
            ]
        );
    }
}

