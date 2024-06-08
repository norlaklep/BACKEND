<?php

namespace Database\Factories;

use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Place>
 */
class PlaceFactory extends Factory
{
    protected $model = Place::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->unique()->sentence(3),
            'description' => $this->faker->paragraph,
            'address' => $this->faker->address,
            'address_link' => $this->faker->url,
            'image_placeholder' => $this->faker->imageUrl(640, 480),
            'image_gallery' => [$this->faker->imageUrl(640, 480), $this->faker->imageUrl(640, 480)],
            'user_id' => User::factory(),  // Assuming you have a User factory
        ];
    }
}
