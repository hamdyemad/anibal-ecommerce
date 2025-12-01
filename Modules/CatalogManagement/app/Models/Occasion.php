<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\BaseModel;
use App\Models\Traits\HumanDates;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Occasion extends BaseModel
{
    use HasFactory, SoftDeletes, HumanDates, Translation;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Vendor relationship
     */
    public function vendor()
    {
        return $this->belongsTo(\Modules\Vendor\app\Models\Vendor::class);
    }

    /**
     * Products relationship
     */
    public function products()
    {
        return $this->belongsToMany(
            VendorProduct::class,
            'occasion_products',
            'occasion_id',
            'product_id'
        )->withPivot('vendor_product_variant_id')->withTimestamps();
    }

    /**
     * Occasion products relationship
     */
    public function occasionProducts()
    {
        return $this->hasMany(OccasionProduct::class);
    }

    /**
     * Get name attribute
     */
    public function getNameAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }

    /**
     * Get title attribute
     */
    public function getTitleAttribute()
    {
        return $this->getTranslation('title', app()->getLocale());
    }

    /**
     * Get sub title attribute
     */
    public function getSubTitleAttribute()
    {
        return $this->getTranslation('sub_title', app()->getLocale());
    }

    /**
     * Scope for active occasions
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Scope for filtering
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        // Apply search filter
        if (!empty($filters['search'])) {
            $query->whereHas('translations', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('title', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Apply active filter
        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('is_active', $filters['active']);
        }

        // Apply date range filters
        if (!empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (!empty($filters['created_until'])) {
            $query->whereDate('created_at', '<=', $filters['created_until']);
        }

        return $query;
    }
}
