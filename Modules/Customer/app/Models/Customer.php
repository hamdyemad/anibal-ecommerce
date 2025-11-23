<?php

namespace Modules\Customer\app\Models;

use App\Models\Traits\HumanDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable, HasApiTokens, HumanDates;

    protected $guarded = [];

    protected $appends = ['full_name'];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
        'status' => 'boolean',
    ];

    // /**
    //  * Override the tokens relationship to use customer_access_tokens table
    //  */
    // public function tokens()
    // {
    //     return $this->hasMany(CustomerAccessToken::class, 'customer_id', 'id');
    // }

    /**
     * Mutator: Hash password before storing in database
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Get FCM tokens for this customer
     */
    public function fcmTokens()
    {
        return $this->hasMany(CustomerFcmToken::class);
    }

    /**
     * Get addresses for this customer
     */
    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    /**
     * Get primary address
     */
    public function primaryAddress()
    {
        return $this->hasOne(CustomerAddress::class)->where('is_primary', true);
    }

    /**
     * Scope to get active customers only
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
