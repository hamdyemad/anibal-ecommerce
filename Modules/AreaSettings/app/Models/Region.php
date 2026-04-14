<?php

namespace Modules\AreaSettings\app\Models;

use App\Models\BaseModel;
use App\Models\Traits\HumanDates;
use App\Traits\HasSlug;
use App\Traits\Translation;
use App\Traits\ClearsApiCache;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\CatalogManagement\app\Models\VariantStock;
use Modules\CatalogManagement\app\Models\VendorProductVariantStock;
use Modules\Order\app\Models\OrderFulfillment;

class Region extends BaseModel
{
    use Translation, SoftDeletes, HumanDates, HasSlug, ClearsApiCache;

    protected $table = 'regions';
    protected $guarded = [];

    // Start Relations
    public function subRegions() {
        return $this->hasMany(SubRegion::class, 'region_id');
    }

    public function city() {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function stocks() {
        return $this->hasMany(VendorProductVariantStock::class, 'region_id');
    }

    public function fulfillments() {
        return $this->hasMany(OrderFulfillment::class, 'region_id');
    }
    // End Relations


    // Start Geters
    public function getNameAttribute() {
        return $this->getTranslation('name', app()->getLocale());
    }
    // End Geters

    /**
     * Get available stock for a variant in this region
     * Available = Total Stock - Booked Stock (from stock_bookings with status 'booked')
     */
    public function getAvailableStockForVariant($variantId)
    {
        // Get total stock for this variant in this region
        $variantStock = $this->stocks()
            ->where('vendor_product_variant_id', $variantId)
            ->first();

        $totalStock = $variantStock ? $variantStock->quantity : 0;

        // Get booked stock from stock_bookings (only 'booked' status, not yet allocated)
        $bookedStock = \Modules\CatalogManagement\app\Models\StockBooking::where('vendor_product_variant_id', $variantId)
            ->where('region_id', $this->id)
            ->where('status', \Modules\CatalogManagement\app\Models\StockBooking::STATUS_BOOKED)
            ->sum('booked_quantity');

        return max(0, $totalStock - $bookedStock);
    }

    public function scopeByVendor(Builder $query, $vendorIdentifier)
    {
        return $query->whereHas('city.country.vendors', function($q) use ($vendorIdentifier) {
            $q
            ->where('vendors.id', $vendorIdentifier)
                ->orWhere('vendors.slug', $vendorIdentifier)
                ;
        });

    }

    /**
     * Override filter scope to add region-specific filters
     * Calls parent filter from HasFilterScopes trait and adds custom filters
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        // Call parent filter scope from HasFilterScopes trait
        // Filter by city
        if (!empty($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }

        // Filter by vendor (through city.country.vendors)
        if (!empty($filters['vendor_id'])) {
           $query->whereHas('city.country.vendors', function($q) use ($filters) {
                $q->where('id', $filters['vendor_id']);
            });
        }


        return $query;
    }
    // End Scopes

    /**
     * Get cache patterns to clear when region is modified
     */
    protected function getCachePatterns(): array
    {
        return [
            'api_regions_',
            'api_subregions_', // SubRegions depend on regions
        ];
    }
}
