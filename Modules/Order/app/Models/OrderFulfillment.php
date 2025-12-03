<?php

namespace Modules\Order\app\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\AreaSettings\app\Models\Region;

class OrderFulfillment extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'order_product_id',
        'region_id',
        'allocated_quantity',
        // 'status',
        'notes',
    ];

    /**
     * Get the order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the order product.
     */
    public function orderProduct(): BelongsTo
    {
        return $this->belongsTo(OrderProduct::class);
    }

    /**
     * Get the region.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Scope to filter by status.
     */
    // public function scopeByStatus($query, $status)
    // {
    //     return $query->where('status', $status);
    // }

    /**
     * Scope to filter by order.
     */
    public function scopeByOrder($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }
}
