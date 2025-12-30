<?php

namespace Modules\CatalogManagement\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorProductTax extends Model
{
    protected $table = 'vendor_product_taxes';

    protected $fillable = [
        'vendor_product_id',
        'tax_id',
    ];

    public function vendorProduct(): BelongsTo
    {
        return $this->belongsTo(VendorProduct::class);
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }
}
