<?php

namespace Modules\SystemSetting\app\Models;

use App\Models\Traits\HumanDates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PointsSetting extends Model
{
    use SoftDeletes, HumanDates;

    protected $table = 'points_settings';
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'points_value' => 'decimal:2',
        'welcome_points' => 'decimal:2',
    ];

    /**
     * Get the currency for this points setting
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}
