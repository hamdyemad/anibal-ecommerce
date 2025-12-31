<?php

namespace App\Models;

use App\Models\Traits\HumanDates;
use App\Models\Traits\CountryCheckIdTrait;
use App\Models\Traits\AutoStoreCountryId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotification extends Model
{
    use HumanDates, CountryCheckIdTrait, AutoStoreCountryId;

    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'user_id',
        'vendor_id',
        'icon',
        'color',
        'title',
        'description',
        'url',
        'data',
        'is_read',
        'read_at',
        'country_id',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the notifiable model
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user (admin)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for specific type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for admin notifications (no specific user/vendor)
     */
    public function scopeForAdmin($query)
    {
        return $query->whereNull('user_id')->whereNull('vendor_id');
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhereNull('user_id');
        });
    }

    /**
     * Scope for specific vendor
     */
    public function scopeForVendor($query, int $vendorId)
    {
        return $query->where(function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId)
              ->orWhereNull('vendor_id');
        });
    }

    /**
     * Mark as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Create notification helper
     */
    public static function notify(
        string $type,
        string $title,
        ?string $description = null,
        ?string $url = null,
        string $icon = 'uil-bell',
        string $color = 'primary',
        ?Model $notifiable = null,
        ?array $data = null,
        ?int $userId = null,
        ?int $vendorId = null
    ): self {
        return self::create([
            'type' => $type,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'notifiable_id' => $notifiable?->id,
            'user_id' => $userId,
            'vendor_id' => $vendorId,
            'icon' => $icon,
            'color' => $color,
            'title' => $title,
            'description' => $description,
            'url' => $url,
            'data' => $data,
        ]);
    }
}
