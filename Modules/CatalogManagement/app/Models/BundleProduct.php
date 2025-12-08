<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\BaseModel;
use App\Models\Traits\HumanDates;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BundleProduct extends BaseModel
{
    use HasFactory, SoftDeletes, Translation, HumanDates;

    protected $guarded = [];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function bundle()
    {
        return $this->belongsTo(Bundle::class);
    }

    public function vendorProductVariant()
    {
        return $this->belongsTo(VendorProductVariant::class);
    }

    /**
     * Get type attribute
     */
    public function getTypeAttribute()
    {
        return 'bundle_product';
    }
}
