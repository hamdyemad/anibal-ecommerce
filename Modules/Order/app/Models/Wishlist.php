<?php

namespace Modules\Order\app\Models;

use App\Models\BaseModel;
use App\Models\Traits\HumanDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\CatalogManagement\app\Models\VendorProduct;
use App\Models\Traits\CountryCheckIdTrait;
use App\Models\Traits\AutoStoreCountryId;


use Modules\Customer\app\Models\Customer;

class Wishlist extends BaseModel
{
    use HasFactory, HumanDates, AutoStoreCountryId, CountryCheckIdTrait;

    protected $fillable = [
        'customer_id',
        'vendor_product_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the customer that owns the wishlist item.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the vendor product that is wishlisted.
     */
    public function vendorProduct(): BelongsTo
    {
        return $this->belongsTo(VendorProduct::class, 'vendor_product_id');
    }

    /**
     * Scope to filter by customer
     */
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope to filter by vendor product
     */
    public function scopeByVendorProduct($query, $vendorProductId)
    {
        return $query->where('vendor_product_id', $vendorProductId);
    }

    /**
     * Check if a product is in wishlist
     */
    public static function isInWishlist($customerId, $vendorProductId): bool
    {
        return self::where('customer_id', $customerId)
            ->where('vendor_product_id', $vendorProductId)
            ->exists();
    }
}
