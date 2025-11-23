<?php

namespace Modules\Customer\app\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAccessToken extends Model
{
    protected $table = 'customer_access_tokens';
    protected $guarded = [];
    protected $casts = [
        'abilities' => 'json',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
