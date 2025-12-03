<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\AreaSettings\app\Models\Country;

class ModelCountry extends Model
{
    use SoftDeletes;

    protected $table = 'model_countries';
    protected $guarded = [];

    /**
     * Get the parent countryable model
     */
    public function countryable()
    {
        return $this->morphTo();
    }

    /**
     * Get the country
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
