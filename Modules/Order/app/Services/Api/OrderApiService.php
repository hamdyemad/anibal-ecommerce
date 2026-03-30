<?php

namespace Modules\Order\app\Services\Api;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Order\app\Interfaces\Api\OrderApiRepositoryInterface;
use Modules\Order\app\Pipelines\FetchCartItems;
use Modules\Order\app\Pipelines\ValidatePromoCode;
use Modules\Order\app\Pipelines\ValidateProducts;
use Modules\Order\app\Pipelines\FetchUserData;
use Modules\Order\app\Pipelines\CalculateApiProductPrices;
use Modules\Order\app\Pipelines\CalculateExtras;
use Modules\Order\app\Pipelines\CalculateFinalTotal;
use Modules\Order\app\Pipelines\CalculateShipping;
use Modules\Order\app\Pipelines\CreateOrder;
use Modules\Order\app\Pipelines\SyncOrderProducts;
use Modules\Order\app\Pipelines\UpdateProductSales;
use Modules\Order\app\Pipelines\EmptyCart;
use Modules\Order\app\Pipelines\CalculatePointsUsagePipeline;
use Modules\Order\app\Pipelines\ValidateDiscountAgainstRemaining;

class OrderApiService
{
    public function __construct(
        private OrderApiRepositoryInterface $orderRepository
    ) {}

    /**
     * Create a new order via API using pipeline pattern
     * Gets cart items and continues with pipeline
     */
    public function checkout(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Determine if this is a guest checkout
            $isGuest = $data['is_guest'] ?? false;
            
            if ($isGuest) {
                // Guest checkout - no authentication required
                $data['selected_customer_id'] = null;
                $data['customer_type'] = "external";
                $data['external_customer_name'] = trim(($data['guest_first_name'] ?? '') . ' ' . ($data['guest_last_name'] ?? ''));
                $data['external_customer_email'] = $data['guest_email'] ?? null;
                $data['external_customer_phone'] = $data['guest_phone'] ?? null;
                $data['external_customer_address'] = $data['guest_address'] ?? null;
                $data['external_city_id'] = $data['guest_city_id'] ?? null;
                $data['external_region_id'] = $data['guest_region_id'] ?? null;
                $data['external_country_id'] = $data['guest_country_id'] ?? null;
                
                // Guest users cannot use points
                $data['use_point'] = false;
            } else {
                // Existing customer checkout
                $data['selected_customer_id'] = Auth::id();
                $data['customer_type'] = "existing";
            }

            $result = app(Pipeline::class)
                ->send([
                    'data' => $data,
                    'context' => [],
                ])
                ->through([
                    FetchCartItems::class,
                    ValidatePromoCode::class,
                    ValidateProducts::class,
                    FetchUserData::class,
                    CalculateApiProductPrices::class,
                    CalculateShipping::class,
                    CalculateExtras::class,
                    ValidateDiscountAgainstRemaining::class,
                    CalculatePointsUsagePipeline::class,
                    CalculateFinalTotal::class,
                    CreateOrder::class,
                    SyncOrderProducts::class,
                    UpdateProductSales::class,
                    EmptyCart::class,
                ])
                ->thenReturn();

            return $result['context']['order'];
        });
    }

    /**
     * Get customer's orders with filtering
     */
    public function getMyOrders(array $filters)
    {
        return $this->orderRepository->getCustomerOrders($filters);
    }

    /**
     * Get order details by ID
     */
    public function getOrderDetails($orderId)
    {
        return $this->orderRepository->getCustomerOrderById(Auth::id(), $orderId);
    }

    /**
     * Cancel order - only if ALL vendors have 'new' stage
     */
    public function cancelOrder($orderId)
    {
        return $this->orderRepository->cancelOrder(Auth::id(), $orderId);
    }

    /**
     * Return/Refund order - only for vendors with 'deliver' stage
     */
    public function returnOrder($orderId)
    {
        return $this->orderRepository->refundOrder(Auth::id(), $orderId);
    }

    /**
     * Check promo code validity
     */
    public function checkPromoCode(string $code)
    {
        return $this->orderRepository->validatePromoCode($code, Auth::id());
    }
}
