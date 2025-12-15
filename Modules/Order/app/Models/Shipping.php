<?php

namespace Modules\Order\app\Models;

use App\Models\BaseModel;
use App\Models\Traits\HumanDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CategoryManagment\app\Models\Category;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\AreaSettings\app\Models\City;
use Modules\AreaSettings\app\Models\Country;

class Shipping extends BaseModel
{
    use HasFactory, HumanDates, Translation, SoftDeletes;

    protected $table = 'shippings';

    protected $fillable = ['cost', 'active', 'city_id', 'category_id', 'country_id'];

    protected $translatable = ['name'];

    protected $appends = ['name_ar'];

    /**
     * Get the city that owns the shipping.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the category that owns the shipping.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
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
