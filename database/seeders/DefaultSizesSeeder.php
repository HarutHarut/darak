<?php

namespace Database\Seeders;

use App\Models\Size;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultSizesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Size::query()->create([
            'name' => ["en" => "small"],
            "desc" => [],
            'verified' => 1,
        ]);

        Size::query()->create([
            'name' => ["en" => "medium"],
            "desc" => [],
            'verified' => 1,
        ]);

        Size::query()->create([
            'name' => ["en" => "large"],
            "desc" => [],
            'verified' => 1,
        ]);
    }
}
