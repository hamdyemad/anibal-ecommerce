<?php

namespace Modules\Order\app\Models;

use App\Models\BaseModel;
use App\Models\Traits\AutoStoreCountryId;
use App\Models\Traits\CountryCheckIdTrait;
use App\Models\Traits\HumanDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CategoryManagment\app\Models\Category;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\AreaSettings\app\Models\City;
use Modules\AreaSettings\app\Models\Country;

class Shipping extends BaseModel
{
    use HasFactory, HumanDates, Translation, SoftDeletes, AutoStoreCountryId, CountryCheckIdTrait;

    protected $table = 'shippings';

    protected $fillable = ['cost', 'active', 'country_id'];

    protected $translatable = ['name'];

    protected $appends = ['name_ar'];

    /**
     * Get the cities associated with the shipping.
     */
    public function cities()
    {
        return $this->belongsToMany(City::class, 'shipping_cities', 'shipping_id', 'city_id')
            ->withTimestamps();
    }

    /**
     * Get the categories associated with the shipping.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'shipping_categories', 'shipping_id', 'category_id')
            ->withTimestamps();
    }

    /**
     * Get the country that owns the shipping.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the Arabic name (title in Arabic)
     */
    public function getNameArAttribute()
    {
        return $this->getTranslation('title', 'ar');
    }

}
