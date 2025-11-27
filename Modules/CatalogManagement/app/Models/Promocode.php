<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\Traits\HumanDates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promocode extends Model
{
    use HasFactory, HumanDates;

    protected $guarded = [];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
        'value' => 'decimal:2',
    ];
}
