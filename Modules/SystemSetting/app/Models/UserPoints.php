<?php

namespace Modules\SystemSetting\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPoints extends Model
{
    use SoftDeletes;

    protected $table = 'user_points';
    protected $guarded = [];

    protected $casts = [
        'total_points' => 'decimal:2',
        'earned_points' => 'decimal:2',
        'redeemed_points' => 'decimal:2',
        'expired_points' => 'decimal:2',
    ];

    /**
     * Get the user that owns the points
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all transactions for this user's points
     */
    public function transactions()
    {
        return $this->hasMany(UserPointsTransaction::class, 'user_id', 'user_id');
    }

    /**
     * Get available points (total - redeemed - expired)
     */
    public function getAvailablePointsAttribute()
    {
        return $this->total_points - $this->redeemed_points - $this->expired_points;
    }
}
