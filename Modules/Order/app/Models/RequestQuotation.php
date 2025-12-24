<?php

namespace Modules\Order\app\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\CountryCheckIdTrait;
use App\Models\Traits\HumanDates;
use App\Models\Traits\AutoStoreCountryId;
use Modules\AreaSettings\app\Models\Country;

class RequestQuotation extends BaseModel
{
    use HasFactory, HumanDates, AutoStoreCountryId, CountryCheckIdTrait;

    const STATUS_NOT_CREATED = 'not_created';
    const STATUS_CREATED = 'created';
    const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'notes',
        'file',
        'status',
        'country_id',
        'order_id',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        return $query;
    }

    public function scopeNotArchived(Builder $query): Builder
    {
        return $query->where('status', '!=', self::STATUS_ARCHIVED);
    }

    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ARCHIVED);
    }
}
