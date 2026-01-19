<?php

namespace Modules\Order\app\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorOrderStageHistory extends BaseModel
{
    protected $fillable = [
        'vendor_order_stage_id',
        'old_stage_id',
        'new_stage_id',
        'user_id',
        'notes',
    ];

    /**
     * Get the vendor order stage
     */
    public function vendorOrderStage(): BelongsTo
    {
        return $this->belongsTo(VendorOrderStage::class);
    }

    /**
     * Get the old stage
     */
    public function oldStage(): BelongsTo
    {
        return $this->belongsTo(OrderStage::class, 'old_stage_id')->withoutGlobalScope('country_filter');
    }

    /**
     * Get the new stage
     */
    public function newStage(): BelongsTo
    {
        return $this->belongsTo(OrderStage::class, 'new_stage_id')->withoutGlobalScope('country_filter');
    }

    /**
     * Get the user who made the change
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
