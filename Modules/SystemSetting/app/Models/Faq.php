<?php

namespace Modules\SystemSetting\app\Models;

use App\Models\Traits\HumanDates;
use App\Models\Traits\AutoStoreCountryId;
use App\Models\Traits\CountryCheckIdTrait;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use Translation, AutoStoreCountryId, CountryCheckIdTrait, SoftDeletes, HumanDates;

    protected $table = 'faqs';
    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get the FAQ question
     */
    public function getQuestionAttribute()
    {
        return $this->getTranslation('question', app()->getLocale());
    }

    /**
     * Get the FAQ answer
     */
    public function getAnswerAttribute()
    {
        return $this->getTranslation('answer', app()->getLocale());
    }

    /**
     * Scope for filtering
     */
    public function scopeFilter(Builder $query, $filters)
    {
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query->whereHas('translations', function($query) use ($filters) {
                $query->where('lang_value', 'like', "%{$filters['search']}%");
            });
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
