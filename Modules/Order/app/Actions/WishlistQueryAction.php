<?php

namespace Modules\Order\app\Actions;

use Modules\Order\app\Models\Wishlist;

class WishlistQueryAction
{
    /**
     * Build wishlist query for a customer with filters
     */
    public function handle($customerId, array $filters = [])
    {
        $query = Wishlist::query()
            ->byCustomer($customerId)
            ->with(['vendorProduct.product', 'vendorProduct.vendor'])
            ->orderBy('created_at', 'desc')->filter($filters);

        return $query;
    }
}
