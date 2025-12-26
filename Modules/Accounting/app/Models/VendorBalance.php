<?php

namespace Modules\Accounting\app\Models;

use App\Models\Traits\HumanDates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorBalance extends Model
{
    use HasFactory, SoftDeletes, HumanDates;

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
        return $this->belongsTo(\Modules\Vendor\app\Models\Vendor::class);
    }

    public function user()
    {
        return $this->hasOneThrough(
            \App\Models\User::class,
            \Modules\Vendor\app\Models\Vendor::class,
            'id',
            'id',
            'vendor_id',
            'user_id'
        );
    }

    public function updateBalance($earnings, $commission)
    {
        $this->total_earnings += $earnings;
        $this->commission_deducted += $commission;
        $this->available_balance = $this->total_earnings - $this->commission_deducted - $this->withdrawn_amount;
        $this->save();
    }
}
