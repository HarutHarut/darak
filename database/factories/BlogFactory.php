<?php

namespace Database\Factories;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;


class BlogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Blog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        Storage::makeDirectory('blogs/logo');
        $image = $this->faker->image();
        $imageFile = new File($image);

        return [
            'logo' => env('APP_URL') . Storage::url(Storage::putFile('blogs/logo', $imageFile)),
            'title' => [
                'en' => $this->faker->name(),
                'ru' => $this->faker->name(),
                'sp' => $this->faker->name(),
                'ch' => $this->faker->name(),
                'am' => $this->faker->name(),
                'de' => $this->faker->name(),
                'fr' => $this->faker->name(),
            ],
            'desc' => [
                'en' => $this->faker->name(),
                'ru' => $this->faker->name(),
                'sp' => $this->faker->name(),
                'ch' => $this->faker->name(),
                'am' => $this->faker->name(),
                'de' => $this->faker->name(),
                'fr' => $this->faker->name(),
            ],
        ];
    }
}
