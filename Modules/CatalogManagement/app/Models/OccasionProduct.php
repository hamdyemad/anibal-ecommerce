<?php

namespace Modules\CatalogManagement\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OccasionProduct extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = true;

    /**
     * Occasion relationship
     */
    public function occasion()
    {
        return $this->belongsTo(Occasion::class);
    }

    /**
     * Product relationship
     */
    public function vendorProduct()
    {
        return $this->belongsTo(VendorProduct::class);
    }

    /**
     * Vendor product variant relationship
     */
    public function vendorProductVariant()
    {
        return $this->belongsTo(VendorProductVariant::class);
    }
}
