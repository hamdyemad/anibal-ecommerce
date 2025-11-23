<?php

namespace Modules\Customer\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\AreaSettings\app\Models\City;
use Modules\AreaSettings\app\Models\Country;
use Modules\AreaSettings\app\Models\Region;
use Modules\AreaSettings\app\Models\Subregion;

class CustomerAddress extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customer_addresses';

    protected $guarded = [];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Get the customer that owns this address
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function subregion()
    {
        return $this->belongsTo(Subregion::class);
    }
}
