<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\Attachment;
use App\Models\BaseModel;
use App\Models\Traits\HumanDates;
use App\Traits\HasSlug;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Vendor\app\Models\Vendor;

class Occasion extends BaseModel
{
    use HasFactory, Translation, SoftDeletes, HumanDates, HasSlug;

    protected $table = 'occasions';
    protected $guarded = [];

    /**
     * Get all attachments for the occasion
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get the vendor that owns this occasion
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the occasion products (variants with special prices)
     */
    public function occasionProducts()
    {
        return $this->hasMany(OccasionProduct::class)->orderBy('position');
    }

    /**
     * Scope for active occasions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
