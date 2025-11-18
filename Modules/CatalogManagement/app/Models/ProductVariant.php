<?php

namespace Modules\CatalogManagement\app\Models;

use App\Traits\HasSlug;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes, Translation, HasSlug;

    protected $table = 'product_variants';
    protected $guarded = [];

    protected $casts = [
        'price' => 'integer',
        'has_discount' => 'boolean',
        'discount_price' => 'integer',
        'discount_end_date' => 'date',
    ];

    // Note: Price-related fields have been moved to VendorProductVariant table
    // This model now only stores variant configuration (key, value, etc.)

    /**
     * The field to generate slug from (for HasSlug trait)
     */
    protected $slugFrom = 'title';

    /**
     * Get the product that owns the variant
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the variant configuration key
     */
    public function variantKey()
    {
        return $this->belongsTo(VariantConfigurationKey::class, 'variant_key_id');
    }

    /**
     * Get the variant configuration value
     */
    public function variantValue()
    {
        return $this->belongsTo(VariantsConfiguration::class, 'variant_value_id');
    }

    /**
     * Get the variant stocks (deprecated - use vendorProductVariants instead)
     */
    public function stocks()
    {
        return $this->hasMany(VariantStock::class);
    }

    /**
     * Get vendor-specific product variants with pricing
     */
    public function vendorProductVariants()
    {
        return $this->hasMany(VendorProductVariant::class);
    }
}
