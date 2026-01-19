<?php

namespace Modules\Refund\app\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Vendor\app\Models\Vendor;

class VendorRefundSetting extends BaseModel
{
    protected $fillable = [
        'vendor_id',
        'refund_processing_days',
        'customer_pays_return_shipping',
    ];

    protected $casts = [
        'refund_processing_days' => 'integer',
        'customer_pays_return_shipping' => 'boolean',
    ];

    /**
     * Get the vendor
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get or create vendor settings
     */
    public static function getForVendor(int $vendorId): self
    {
        return static::firstOrCreate(
            ['vendor_id' => $vendorId],
            [
                'refund_processing_days' => 7,
                'customer_pays_return_shipping' => false,
            ]
        );
    }
}
