<?php

namespace Modules\SystemSetting\app\Models;

use App\Models\Attachment;
use App\Models\Traits\HumanDates;
use App\Models\Traits\AutoStoreCountryId;
use App\Models\Traits\CountryCheckIdTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Translation;

class Slider extends Model
{
    use AutoStoreCountryId, CountryCheckIdTrait, SoftDeletes, HumanDates, Translation;

    protected $table = 'sliders';
    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get all attachments for the slider
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function image()
    {
        return $this->morphOne(Attachment::class, 'attachable')->where('type', 'image');
    }

    /**
     * Get the slider image
     */
    public function getImageAttribute()
    {
        $attachment = $this->attachments()->where('type', 'image')->first();
        return $attachment ? asset('storage/' . $attachment->path) : '';
    }

    public function getTitleAttribute()
    {
        return $this->getTranslation('title', app()->getLocale());
    }

    public function getDescriptionAttribute()
    {
        return $this->getTranslation('description', app()->getLocale());
    }

    /**
     * Scope for filtering
     */
    public function scopeFilter(Builder $query, $filters)
    {
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query->where('slider_link', 'like', "%{$filters['search']}%");
        }

        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('active', $filters['active']);
        }

        if (isset($filters['created_date_from']) && !empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        if (isset($filters['created_date_to']) && !empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        return $query;
    }
}
