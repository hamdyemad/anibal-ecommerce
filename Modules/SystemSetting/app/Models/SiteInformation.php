<?php

namespace Modules\SystemSetting\app\Models;

use App\Models\Traits\HumanDates;
use App\Models\Traits\AutoStoreCountryId;
use App\Models\Traits\CountryCheckIdTrait;
use App\Traits\Translation;
use App\Traits\ClearsApiCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteInformation extends Model
{
    use Translation, AutoStoreCountryId, CountryCheckIdTrait, SoftDeletes, HumanDates, ClearsApiCache;

    protected $table = 'site_information';
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function getAddressAttribute() {
        return $this->getTranslation('address', app()->getLocale());
    }
    /**
     * Scope to filter site information
     */
    public function scopeFilter(Builder $query, $filters = [])
    {
        return $query;
    }

    /**
     * Get cache patterns to clear when site information is modified
     */
    protected function getCachePatterns(): array
    {
        return [
            'api_site_information_',
        ];
    }
}
