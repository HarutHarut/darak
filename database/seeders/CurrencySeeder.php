<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Currency::query()->create([
            'name' => 'USD',
            'icon' => '$'
        ]);

        Currency::query()->create([
            'name' => 'AMD',
            'icon' => 'Դ'
        ]);

        Currency::query()->create([
            'name' => 'EUR',
            'icon' => '€'
        ]);

        Currency::query()->create([
            'name' => 'RUB',
            'icon' => '₽'
        ]);

    }
}
