<?php

namespace Modules\Customer\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerFcmToken extends Model
{
    use HasFactory;

    protected $table = 'customer_fcm_tokens';

    protected $guarded = [];

    /**
     * Get the customer that owns this token
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
