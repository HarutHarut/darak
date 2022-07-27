<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        City::factory(3)->create();
    }
}
