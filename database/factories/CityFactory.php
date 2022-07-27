<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\File;


class CityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = City::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        Storage::makeDirectory('cities/logo');
//        $image = $this->faker->image();
//        $imageFile = new File($image);
        return [
            'name' => $this->faker->name(),
            'lat' => $this->faker->latitude(),
            'lng' => $this->faker->longitude(),
            'top' => 1,
//            'logo' => env('APP_URL') . Storage::url(Storage::putFile('cities/logo', $imageFile)),
            'logo' => 'https://img.freepik.com/free-photo/view-manhattan-sunset-new-york-city_268835-463.jpg?w=2000',
        ];
    }
}
