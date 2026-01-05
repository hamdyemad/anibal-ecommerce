<?php

namespace Modules\SystemSetting\app\Models;

use App\Models\Attachment;
use App\Models\Traits\HumanDates;
use App\Models\Traits\AutoStoreCountryId;
use App\Models\Traits\CountryCheckIdTrait;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ad extends Model
{
    use Translation, AutoStoreCountryId, CountryCheckIdTrait, SoftDeletes, HumanDates;

    protected $table = 'ads';
    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
        'type' => 'array',
    ];

    /**
     * Get all attachments for the ad
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get the ad title
     */
    public function getTitleAttribute()
    {
        return $this->getTranslation('title', app()->getLocale());
    }

    /**
     * Get the ad subtitle
     */
    public function getSubtitleAttribute()
    {
        return $this->getTranslation('subtitle', app()->getLocale());
    }

    /**
     * Get the ad image
     */
    public function getImageAttribute()
    {
        $attachment = $this->attachments()->where('type', 'image')->first();
        return $attachment ? $attachment->path : null;
    }

    /**
     * Get position label (English)
     */
    public function getPositionLabelAttribute()
    {
        $positions = self::getPositions();
        return $positions[$this->position] ?? $this->position;
    }


    public function scopeActive(Builder $query) {
        return $query->where('active', 1);
    }
    /**
     * Scope for filtering
     */
    public function scopeFilter(Builder $query, $filters)
    {
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query->whereHas('translations', function($query) use ($filters) {
                $query->where('lang_value', 'like', "%{$filters['search']}%");
            })
            ->orWhere('link', 'like', "%{$filters['search']}%");
        }

        if (isset($filters['position']) && !empty($filters['position'])) {
            $query->where('position', $filters['position']);
        }
        if (isset($filters['type']) && !empty($filters['type'])) {
            $query->whereJsonContains('type', $filters['type']);
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

    /**
     * Available positions (English labels for API)
     */
    public static function getPositions()
    {
        return [
            'homepage_mid_content_banner' => 'Homepage Mid-Content Banner',
            'homepage_main_right_banner' => 'Homepage Main Right Banner',
            'homepage_left_lower_ad_card' => 'Homepage Left Lower Ad Card',
            'homepage_left_upper_ad_card' => 'Homepage Left Upper Ad Card',
            'middle_home_ad' => 'Middle Home Ad',
        ];
    }
}
