<?php

namespace Modules\CatalogManagement\app\Observers;

use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\Tax;

class VendorProductObserver
{
    /**
     * Handle the VendorProduct "created" event.
     */
    public function created(VendorProduct $vendorProduct): void
    {
        $this->syncActiveTaxes($vendorProduct);
    }

    /**
     * Handle the VendorProduct "updated" event.
     */
    public function updated(VendorProduct $vendorProduct): void
    {
        $this->syncActiveTaxes($vendorProduct);
    }

    /**
     * Sync all active taxes to the vendor product.
     */
    protected function syncActiveTaxes(VendorProduct $vendorProduct): void
    {
        // Get all active taxes for the current country
        $activeTaxIds = Tax::where('is_active', true)->pluck('id')->toArray();
        
        // Sync taxes (this will add missing taxes and keep existing ones)
        $vendorProduct->taxes()->sync($activeTaxIds);
    }
}
