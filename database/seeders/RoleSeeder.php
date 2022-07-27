<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        Role::query()
            ->insert([
                [
                    "name" => "admin",
                    "title" => "Admin"
                ],
                [
                    "name" => "user",
                    "title" => "User"
                ],
                [
                    "name" => "business_owner",
                    "title" => "Business owner"
                ],
            ]);

    }
}
