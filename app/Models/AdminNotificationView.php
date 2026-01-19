<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotificationView extends Model
{
    protected $fillable = [
        'admin_notification_id',
        'user_id',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    /**
     * Get the admin notification
     */
    public function adminNotification(): BelongsTo
    {
        return $this->belongsTo(AdminNotification::class);
    }

    /**
     * Get the user who viewed
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
