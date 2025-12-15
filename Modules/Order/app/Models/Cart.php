<?php

namespace Modules\Order\app\Models;

use App\Models\BaseModel;
use App\Models\Traits\HumanDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\VendorProductVariant;
use Modules\Customer\app\Models\Customer;

class Cart extends BaseModel
{
    use HasFactory, HumanDates;

    protected $fillable = [
        'customer_id',
        'vendor_product_id',
        'vendor_product_variant_id',
        'type',
        'bundle_id',
        'occasion_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the customer that owns the cart item.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the vendor product in the cart.
     */
    public function vendorProduct(): BelongsTo
    {
        return $this->belongsTo(VendorProduct::class, 'vendor_product_id');
    }

    /**
     * Get the vendor product variant in the cart.
     */
    public function vendorProductVariant(): BelongsTo
    {
        return $this->belongsTo(VendorProductVariant::class, 'vendor_product_variant_id');
    }

    /**
     * Get the bundle if this is a bundle cart item.
     */
    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Bundle::class, 'bundle_id');
    }

    /**
     * Get the occasion if this is an occasion cart item.
     */
    public function occasion(): BelongsTo
    {
        return $this->belongsTo(Occasion::class, 'occasion_id');
    }

    /**
     * Scope to filter by customer
     */
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope to filter by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by vendor product
     */
    public function scopeByVendorProduct($query, $vendorProductId)
    {
        return $query->where('vendor_product_id', $vendorProductId);
    }

    /**
     * Check if a product is in cart
     */
    public static function isInCart($customerId, $vendorProductId, $vendorProductVariantId = null, $type = 'product', $bundleId = null, $occasionId = null): bool
    {
        $query = self::where('customer_id', $customerId)
            ->where('vendor_product_id', $vendorProductId)
            ->where('type', $type);

        if ($vendorProductVariantId) {
            $query->where('vendor_product_variant_id', $vendorProductVariantId);
        }

        if ($bundleId) {
            $query->where('bundle_id', $bundleId);
        }

        if ($occasionId) {
            $query->where('occasion_id', $occasionId);
        }

        return $query->exists();
    }

    /**
     * Get total items count for customer
     */
    public static function getTotalItems($customerId): int
    {
        return self::where('customer_id', $customerId)->count();
    }
}
