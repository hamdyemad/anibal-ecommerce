<?php

namespace Modules\Vendor\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorCommission extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vendor_commission';
    protected $guarded = [];

    /**
     * Get the vendor that owns the commission
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
