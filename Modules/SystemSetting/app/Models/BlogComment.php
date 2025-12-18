<?php

namespace Modules\SystemSetting\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\AutoStoreCountryId;
use App\Models\Traits\CountryCheckIdTrait;
use App\Models\Traits\HumanDates;
use Modules\Customer\app\Models\Customer;

class BlogComment extends Model
{
    use HasFactory, SoftDeletes, AutoStoreCountryId, CountryCheckIdTrait, HumanDates;

    protected $fillable = [
        'blog_id',
        'customer_id',
        'comment',
        'active',
        'country_id',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get the blog that owns the comment.
     */
    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    /**
     * Get the customer that owns the comment.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Scope a query to only include active comments.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
