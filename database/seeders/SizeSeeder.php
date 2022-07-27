<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Size::create([
            'name' => ["en" => "small"],
            'desc' => ["en" => "small"],
            'verified' => 1
        ]);
        Size::create([
            'name' => ["en" => "medium"],
            'desc' => ["en" => "medium"],
            'verified' => 1
        ]);
        Size::create([
            'name' => ["en" => "large"],
            'desc' => ["en" => "large"],
            'verified' => 1
        ]);
    }
}
