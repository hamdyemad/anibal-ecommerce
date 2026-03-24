<?php

namespace Modules\Order\app\Actions;

use Modules\Order\app\Models\Wishlist;
use Modules\CatalogManagement\app\Models\VendorProduct;

class WishlistQueryAction
{
    /**
     * Build wishlist query for a customer with filters
     * Uses the same relations as ProductListQueryAction for consistency
     */
    public function handle($customerId, array $filters = [])
    {
        $query = Wishlist::query()
            ->byCustomer($customerId)
            ->with([
                'vendorProduct' => function ($q) {
                    $q->active()
                        ->status(VendorProduct::STATUS_APPROVED)
                        ->with([
                            'product.translations',
                            'product.mainImage',
                            'product.brand.translations',
                            'variants',
                            'vendor.translations',
                            'taxes.translations',
                        ])
                        ->withCount(['reviews' => function($q) {
                            $q->withoutGlobalScope('country_filter');
                        }])
                        ->withAvg(['reviews' => function($q) {
                            $q->withoutGlobalScope('country_filter');
                        }], 'star');
                }
            ])
            ->whereHas('vendorProduct', function ($q) {
                $q->active()
                    ->status(VendorProduct::STATUS_APPROVED)
                    ->whereHas('product');
            })
            ->orderBy('created_at', 'desc');

        if (!empty($filters)) {
            $query->filter($filters);
        }

        return $query;
    }
}
