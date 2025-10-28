<?php

namespace Modules\AreaSettings\app\Models;

use App\Traits\Translation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use Translation, SoftDeletes;
    
    protected $table = 'countries';
    protected $guarded = [];

    public function cities() {
        return $this->hasMany(City::class, 'country_id');
    }
}
