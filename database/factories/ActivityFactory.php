<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\CategoryManagment\app\Models\Activity;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\CategoryManagment\app\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Activity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => $this->faker->unique()->slug(),
            'active' => $this->faker->boolean(80), // 80% chance of being active
        ];
    }
}
