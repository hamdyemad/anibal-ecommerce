<?php

namespace Modules\Order\app\Actions;

use Illuminate\Support\Facades\Auth;
use Modules\Order\app\Models\Order;

class OrderQueryAction
{
    /**
     * Build order query for a customer with filters
     */
    public function handle(array $filters = [])
    {
        $query = Order::where('customer_id', Auth::id())
            ->with([
                'products', 
                'products.order',
                'products.order.vendorStages',
                'products.order.vendorStages.stage',
                'products.order.vendorStages.history',
                'products.order.vendorStages.history.newStage',
                'products.vendorProduct', 
                'products.vendorProductVariant.variantConfiguration.key',
                'products.vendorProductVariant.variantConfiguration.parent_data.key',
                'products.stage' => function($q) {
                    $q->withoutGlobalScopes();
                },
                'payments',
                'vendorStages',
                'vendorStages.history',
                'vendorStages.history.newStage'
            ])
            ->orderBy('created_at', 'desc')
            ->filter($filters);

        return $query;
    }
}
