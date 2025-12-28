<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\Traits\HumanDates;
use App\Models\Traits\AutoStoreCountryId;
use App\Models\Traits\CountryCheckIdTrait;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Tax extends Model
{
    use HasFactory, Translation, SoftDeletes, HumanDates, AutoStoreCountryId, CountryCheckIdTrait;

    protected $fillable = [
        'percentage',
        'is_active',
        'country_id',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the vendor products that have this tax
     */
    public function vendorProducts()
    {
        return $this->belongsToMany(VendorProduct::class, 'vendor_product_taxes', 'tax_id', 'vendor_product_id')
            ->withTimestamps();
    }

    public function getNameAttribute()
    {
        return $this->getTranslation('name', app()->getLocale()) ?? '';
    }
}
