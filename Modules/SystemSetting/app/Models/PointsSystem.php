<?php

namespace Modules\SystemSetting\app\Models;

use App\Models\Traits\HumanDates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PointsSystem extends Model
{
    use SoftDeletes, HumanDates;

    protected $table = 'points_systems';
    protected $guarded = [];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    /**
     * Get the single instance of points system
     */
    public static function getInstance()
    {
        return self::first() ?? self::create(['is_enabled' => false]);
    }

    /**
     * Check if points system is enabled
     */
    public static function isEnabled()
    {
        return self::getInstance()->is_enabled ?? false;
    }
}
