<?php

namespace Modules\Accounting\app\Models;

use App\Models\BaseModel;
use App\Models\Traits\HumanDates;
use App\Models\Traits\AutoStoreCountryId;
use App\Models\Traits\CountryCheckIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Vendor\app\Models\Vendor;

class VendorBalance extends BaseModel
{
    use HasFactory, SoftDeletes, HumanDates, AutoStoreCountryId, CountryCheckIdTrait;

    protected $fillable = [
        'vendor_id',
        'total_earnings',
        'commission_deducted',
        'available_balance',
        'withdrawn_amount',
        'country_id'
    ];

    protected $casts = [
        'total_earnings' => 'decimal:2',
        'commission_deducted' => 'decimal:2',
        'available_balance' => 'decimal:2',
        'withdrawn_amount' => 'decimal:2'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function updateBalance($earnings, $commission)
    {
        $this->total_earnings += $earnings;
        $this->commission_deducted += $commission;
        $this->available_balance = $this->total_earnings - $this->commission_deducted - $this->withdrawn_amount;
        $this->save();
    }

    protected function applyCustomSearch(\Illuminate\Database\Eloquent\Builder $query, string $search): \Illuminate\Database\Eloquent\Builder
    {
        $query->whereHas('vendor', function($subQ) use ($search) {
            $subQ->where('name', 'like', "%{$search}%")
                 ->orWhere('email', 'like', "%{$search}%");
        });
        
        return $query;
    }

    public function scopeMinBalance($query, $minBalance)
    {
        return $query->where('available_balance', '>=', $minBalance);
    }

    public function scopeFilter($query, array $filters)
    {
        parent::scopeFilter($query, $filters);
        
        if (!empty($filters['min_balance'])) {
            $query->minBalance($filters['min_balance']);
        }
        
        return $query;
    }
}
