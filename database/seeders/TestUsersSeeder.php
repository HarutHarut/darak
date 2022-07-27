<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'role_id' => 1,
            'password' => Hash::make('password'),
        ]);

        User::query()->create([
            'name' => 'user',
            'email' => 'user@gmail.com',
            'email_verified_at' => now(),
            'role_id' => 2,
            'password' => Hash::make('password'),
        ]);

        User::query()->create([
            'name' => 'business',
            'email' => 'business@gmail.com',
            'email_verified_at' => now(),
            'role_id' => 3,
            'password' => Hash::make('password'),
        ]);
    }
}
