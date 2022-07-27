<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Business;
use App\Models\City;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Str;


class BranchFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Branch::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        Storage::makeDirectory('branches/logo');
//        $image = $this->faker->image();
//        $imageFile = new File($image);
        $nameEn = $this->faker->name();

        return [
            'business_id' => Business::query()->inRandomOrder()->first()->id,
            'city_id' => City::query()->inRandomOrder()->first()->id,
            'currency_id' => Currency::query()->inRandomOrder()->first()->id,
            'name' => [
                'en' => $nameEn,
                'ru' => $this->faker->name(),
                'sp' => $this->faker->name(),
                'ch' => $this->faker->name(),
                'am' => $this->faker->name(),
                'de' => $this->faker->name(),
                'fr' => $this->faker->name(),
            ],
            'description' => [
                'en' => $this->faker->name(),
                'ru' => $this->faker->name(),
                'sp' => $this->faker->name(),
                'ch' => $this->faker->name(),
                'am' => $this->faker->name(),
                'de' => $this->faker->name(),
                'fr' => $this->faker->name(),
            ],

            'meta_title' => [
                'en' => $this->faker->realText(mt_rand(10, 60)),
                'ru' => $this->faker->realText(mt_rand(10, 60)),
                'sp' => $this->faker->realText(mt_rand(10, 60)),
                'ch' => $this->faker->realText(mt_rand(10, 60)),
                'am' => $this->faker->realText(mt_rand(10, 60)),
                'de' => $this->faker->realText(mt_rand(10, 60)),
                'fr' => $this->faker->realText(mt_rand(10, 60)),
            ],

            'meta_description' => [
                'en' => $this->faker->realText(mt_rand(100, 150)),
                'ru' => $this->faker->realText(mt_rand(100, 150)),
                'sp' => $this->faker->realText(mt_rand(100, 150)),
                'ch' => $this->faker->realText(mt_rand(100, 150)),
                'am' => $this->faker->realText(mt_rand(100, 150)),
                'de' => $this->faker->realText(mt_rand(100, 150)),
                'fr' => $this->faker->realText(mt_rand(100, 150)),
            ],

            'meta_keywords' => [
                'en' => $this->faker->realText(mt_rand(100, 160)),
                'ru' => $this->faker->realText(mt_rand(100, 160)),
                'sp' => $this->faker->realText(mt_rand(100, 160)),
                'ch' => $this->faker->realText(mt_rand(100, 160)),
                'am' => $this->faker->realText(mt_rand(100, 160)),
                'de' => $this->faker->realText(mt_rand(100, 160)),
                'fr' => $this->faker->realText(mt_rand(100, 160)),
            ],

            'slug' => Str::slug($nameEn),
            'lat' => $this->faker->latitude(),
            'lng' => $this->faker->longitude(),
            'phone' => $this->faker->phoneNumber(),
//            'logo' => env('APP_URL') . Storage::url(Storage::putFile('branches/logo', $imageFile)),
            'logo' => 'https://archello.s3.eu-central-1.amazonaws.com/images/2018/03/09/Nestle-Lockers-1.1520613399.1193.jpg',
            'country' => $this->faker->name(),
            'address' => $this->faker->address(),
        ];
    }
}
