<?php

namespace Modules\Order\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Order\app\Models\Order;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'country_id' => current_country()->id,
            'order_number' => 'ORD-' . $this->faker->unique()->numerify('########'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
