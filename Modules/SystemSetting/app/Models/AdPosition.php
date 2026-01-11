<?php

namespace Modules\SystemSetting\app\Models;

use App\Models\Traits\HumanDates;
use Illuminate\Database\Eloquent\Model;

class AdPosition extends Model
{
    use HumanDates;

    protected $table = 'ads_positions';
    protected $guarded = [];

    /**
     * Get the ads for this position
     */
    public function ads()
    {
        return $this->hasMany(Ad::class, 'ad_position_id');
    }
}
