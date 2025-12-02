<?php

namespace Modules\Order\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Order\database\factories\OrderFactory;
use Modules\Customer\app\Models\Customer;
use Modules\AreaSettings\app\Models\Country;
use Modules\AreaSettings\app\Models\City;
use Modules\AreaSettings\app\Models\Region;

class Order extends Model
{
    use HasFactory;

    public static function newFactory()
    {
        return OrderFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_number',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_address',
        'customer_phone',
        'order_from',
        'payment_type',
        'customer_promo_code_title',
        'customer_promo_code_value',
        'customer_promo_code_type',
        'shipping',
        'total_tax',
        'total_product_price',
        'items_count',
        'total_price',
        'stage_id',
        'country_id',
        'city_id',
        'region_id',
        'refunded_amount',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'shipping' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_product_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'customer_promo_code_value' => 'decimal:2',
    ];

    /**
     * Get the customer associated with the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the order stage.
     */
    public function stage(): BelongsTo
    {
        return $this->belongsTo(OrderStage::class);
    }

    /**
     * Get the country.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the city.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the region.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the order products.
     */
    public function products(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    /**
     * Get the order fulfillments.
     */
    public function fulfillments(): HasMany
    {
        return $this->hasMany(OrderFulfillment::class);
    }

    /**
     * Get the order extra fees and discounts.
     */
    public function extraFeesDiscounts(): HasMany
    {
        return $this->hasMany(OrderExtraFeeDiscount::class);
    }
}
