<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;
use App\Models\User;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = Faker::create();
        return [
            'title' => $faker->sentence,
            'content' => 'test_title',
            "image" => 'image/image.jpg',
            "category_id" => Category::factory()->create()->id,
            'user_id' => User::factory()->create()->id
        ];
    }
}
