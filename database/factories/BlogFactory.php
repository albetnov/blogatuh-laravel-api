<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blog>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name = $this->faker->word();
        $categories = Category::get();
        $categoryBuilder = $categories->random(3)->pluck('id')->toArray();
        $category = "{$categoryBuilder[0]}, {$categoryBuilder[1]}, {$categoryBuilder[2]}";
        return [
            'name' => $name,
            'content' => $this->faker->paragraph(3),
            'categories' => $category
        ];
    }
}
