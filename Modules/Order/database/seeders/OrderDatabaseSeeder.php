<?php

namespace Modules\Order\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderProduct;

class OrderDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء 1000 order
        Order::factory(1000)->create()->each(function ($order) {
            OrderProduct::factory(rand(1, 5))->create([
                'order_id' => $order->id,
            ]);
        });
    }
}
