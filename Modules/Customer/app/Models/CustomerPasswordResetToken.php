<?php

namespace Modules\Customer\app\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPasswordResetToken extends Model
{
    public $timestamps = false;

    protected $table = 'customer_password_reset_tokens';

    protected $primaryKey = 'email';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'email',
        'token',
        'created_at',
        'expires_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'expires_at' => 'datetime',
    ];
}
