<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            [
                'login' => 'admin',
                ],
            [
                'full_name' => 'admin',
                'phone' => '+77777777777',
                'email' => 'admin@admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'avatar' => null,
                'id' => '1',
            ]
        );
    }
}
