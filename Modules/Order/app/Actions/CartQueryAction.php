<?php

namespace Modules\Order\app\Actions;

use Modules\Order\app\Models\Cart;

class CartQueryAction
{
    /**
     * Build cart query for a customer with filters
     */
    public function handle($customerId, array $filters = [])
    {
        $query = Cart::query()
            ->byCustomer($customerId)
            ->with([
                'vendorProduct.product',
                'vendorProduct.vendor',
                'vendorProduct.tax',
                'vendorProductVariant',
                // 'bundle',
                // 'occasion'
            ])
            ->orderBy('created_at', 'desc')
            ->filter($filters);

        return $query;
    }
}
