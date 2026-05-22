<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('login', 'admin')->first();

        if (!$user) {
            $user = new User();
            $user->login = 'admin';
        }

        $user->full_name = 'Администратор Системы';
        $user->phone = '+7(999)-999-99-99';
        $user->email = 'admin@omk-it.ru';
        $user->password = 'password';
        $user->role = 'admin';
        $user->save();
    }
}
