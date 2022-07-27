<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogTranslations;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Blog::factory(4)
           ->create();

    }
}
