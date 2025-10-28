<?php

namespace Modules\AreaSettings\app\Models;

use App\Traits\Translation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use Translation, SoftDeletes;
    
    protected $table = 'regions';
    protected $guarded = [];

    public function subRegions() {
        return $this->hasMany(SubRegion::class, 'region_id');
    }

    public function city() {
        return $this->belongsTo(City::class, 'city_id');
    }


}
