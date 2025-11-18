<?php

namespace Modules\CatalogManagement\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Vendor\app\Models\Vendor;

class VendorProduct extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the vendor that owns this vendor product
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope to get pending vendor products
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get approved vendor products
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get rejected vendor products
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
