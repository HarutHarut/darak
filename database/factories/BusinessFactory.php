<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class BusinessFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Business::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        Storage::makeDirectory('business/logo');

//        $image = $this->faker->image();
//        $imageFile = new File($image);
        return [
            'user_id' => User::query()->where('role_id','=',3)->inRandomOrder()->first()->id,
            'city_id' => City::query()->inRandomOrder()->first()->id,

            'name' => [
                'en' => $this->faker->name(),
                'ru' => $this->faker->name(),
                'sp' => $this->faker->name(),
                'ch' => $this->faker->name(),
                'am' => $this->faker->name(),
                'de' => $this->faker->name(),
                'fr' => $this->faker->name(),
            ],

            'lat' => $this->faker->latitude(),
            'lng' => $this->faker->longitude(),
            'phone' => $this->faker->phoneNumber(),
            'address' =>  $this->faker->address(),
            'rating' =>  $this->faker->numberBetween(0,5),
            'status' => 1 ,
//            'logo' => env('APP_URL') . Storage::url(Storage::putFile('business/logo', $imageFile)),
            'logo' => 'https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxzZWFyY2h8Mnx8Y2l0eXxlbnwwfHwwfHw%3D&w=1000&q=80',
        ];
    }
}
