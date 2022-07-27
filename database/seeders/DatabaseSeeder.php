<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            TestUsersSeeder::class,
            CitySeeder::class,
            CurrencySeeder::class,
            BusinessSeeder::class,
            BranchSeeder::class,
            StaticSeeder::class,
            SizeSeeder::class
        ]);
    }
}
