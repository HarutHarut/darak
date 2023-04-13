<?php

namespace App\Console\Commands;

use App\Models\City;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CitiesSlug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'city_slug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cities = City::get();

        foreach ($cities as $item) {

            $slug = Str::slug($item->name, '-');

            $i = 1;
            $slugTemp = $slug;
            while (City::where('slug', $slug)->exists()) {

                $slug = $slugTemp . "-" . $i;
                $i++;
            }
            $item->slug = $slug;
            $item->save();
        }
    }
}
