<?php

namespace Modules\AreaSettings\app\Models;

use App\Traits\Translation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubRegion extends Model
{
    use Translation, SoftDeletes;
    
    protected $table = 'subregions';
    protected $guarded = [];


    public function region() {
        return $this->belongsTo(Region::class, 'region_id');
    }
}
