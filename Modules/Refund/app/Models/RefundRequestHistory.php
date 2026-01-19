<?php

namespace Modules\Refund\app\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefundRequestHistory extends BaseModel
{
    protected $fillable = [
        'refund_request_id',
        'old_status',
        'new_status',
        'user_id',
        'notes',
    ];

    /**
     * Get the refund request
     */
    public function refundRequest(): BelongsTo
    {
        return $this->belongsTo(RefundRequest::class);
    }

    /**
     * Get the user who made the change
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
