<?php

namespace Modules\CatalogManagement\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Vendor\app\Models\Vendor;

class VendorProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vendor_product_variants';
    protected $guarded = [];

    protected $casts = [
        'price' => 'integer',
        'has_discount' => 'boolean',
        'discount_price' => 'integer',
        'discount_end_date' => 'date',
    ];

    /**
     * Get the vendor that owns this variant pricing
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the product variant
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Get the vendor product variant stocks
     */
    public function stocks()
    {
        return $this->hasMany(VendorProductVariantStock::class);
    }

    /**
     * Get the effective price (with discount if applicable)
     */
    public function getEffectivePrice()
    {
        if ($this->has_discount && $this->discount_end_date && $this->discount_end_date->isFuture()) {
            return $this->discount_price;
        }

        return $this->price;
    }

    /**
     * Get total stock across all regions for this vendor
     */
    public function getTotalStock()
    {
        return $this->stocks()->sum('stock');
    }
}
