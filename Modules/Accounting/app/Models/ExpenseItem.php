<?php

namespace Modules\Accounting\app\Models;

use App\Models\BaseModel;
use App\Models\Traits\HumanDates;
use App\Models\Traits\AutoStoreCountryId;
use App\Models\Traits\CountryCheckIdTrait;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseItem extends BaseModel
{
    use HasFactory, SoftDeletes, HumanDates, AutoStoreCountryId, CountryCheckIdTrait, Translation;

    protected $fillable = [
        'active',
        'country_id'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeFilter($query, array $filters)
    {
        parent::scopeFilter($query, $filters);

        if (!empty($filters['status'])) {
            $query->where('active', $filters['status']);
        }

        return $query;
    }
}
