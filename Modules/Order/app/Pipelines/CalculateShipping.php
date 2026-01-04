<?php

namespace Modules\Order\app\Pipelines;

use Closure;
use Illuminate\Support\Facades\Log;
use Modules\Order\app\Services\ShippingCalculationService;
use Modules\SystemSetting\app\Models\SiteInformation;

class CalculateShipping
{
    public function __construct(
        private ShippingCalculationService $shippingCalculationService
    ) {}

    /**
     * Calculate shipping cost for order products
     */
    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];

        // Use products_data from context (set by CalculateProductPrices) which has category/department info
        $productsData = $context['products_data'] ?? [];
        
        Log::info('CalculateShipping: products_data', ['count' => count($productsData), 'data' => $productsData]);
        
        if (empty($productsData)) {
            Log::warning('CalculateShipping: No products_data found in context');
            $context['shipping_breakdown'] = [];
            $context['product_shipping'] = [];
            return $next([
                'data' => $data,
                'context' => $context,
            ]);
        }
        
        // Get shipping settings to determine type
        $shippingSettings = SiteInformation::first();
        $groupByType = 'category'; // default
        if ($shippingSettings?->shipping_allow_departments) {
            $groupByType = 'department';
        } elseif ($shippingSettings?->shipping_allow_sub_categories) {
            $groupByType = 'subcategory';
        } elseif ($shippingSettings?->shipping_allow_categories) {
            $groupByType = 'category';
        }
        
        Log::info('CalculateShipping: groupByType', ['type' => $groupByType]);
        
        // Format cart items with type, type_id, type_name based on settings
        $cartItems = [];
        foreach ($productsData as $product) {
            // Determine type_id and type_name based on groupByType
            $typeId = null;
            $typeName = null;
            
            switch ($groupByType) {
                case 'department':
                    $typeId = $product['department_id'] ?? null;
                    $typeName = $product['department_name'] ?? null;
                    break;
                case 'subcategory':
                    $typeId = $product['sub_category_id'] ?? null;
                    $typeName = $product['sub_category_name'] ?? null;
                    break;
                case 'category':
                default:
                    $typeId = $product['category_id'] ?? null;
                    $typeName = $product['category_name'] ?? null;
                    break;
            }
            
            $cartItems[] = [
                'type' => $groupByType,
                'type_id' => $typeId,
                'type_name' => $typeName,
                'product_id' => $product['vendor_product_id'] ?? null,
                'vendor_id' => $product['vendor_id'] ?? null,
                'quantity' => $product['quantity'] ?? 1,
            ];
        }
        
        Log::info('CalculateShipping: cartItems', ['items' => $cartItems]);

        // Calculate shipping for existing customers with address
        if (isset($data['selected_customer_id']) && isset($data['customer_address_id']) && !empty($cartItems)) {
            Log::info('CalculateShipping: Using customer address', [
                'customer_id' => $data['selected_customer_id'],
                'address_id' => $data['customer_address_id']
            ]);
            try {
                $shippingResult = $this->shippingCalculationService->calculateShipping(
                    $data['selected_customer_id'],
                    $data['customer_address_id'],
                    $cartItems
                );

                // Update shipping value in data with calculated value
                $data['shipping'] = $shippingResult['shipping_cost'] ?? ($data['shipping'] ?? 0);
                $context['shipping_breakdown'] = $shippingResult['breakdown'] ?? [];
                $context['product_shipping'] = $shippingResult['product_shipping'] ?? [];
                
                Log::info('CalculateShipping: Result', ['shipping' => $data['shipping'], 'product_shipping' => $context['product_shipping']]);
            } catch (\Exception $e) {
                Log::error('CalculateShipping: Error', ['error' => $e->getMessage()]);
                // If shipping calculation fails, keep the submitted value or use 0
                $data['shipping'] = $data['shipping'] ?? 0;
                $context['shipping_breakdown'] = [];
                $context['product_shipping'] = [];
            }
        }
        // Calculate shipping for external customers with city_id
        elseif (isset($data['external_city_id']) && !empty($cartItems)) {
            Log::info('CalculateShipping: Using external city', ['city_id' => $data['external_city_id']]);
            try {
                $shippingResult = $this->shippingCalculationService->calculateShipping(
                    null,
                    null,
                    $cartItems,
                    $data['external_city_id']
                );

                // Update shipping value in data with calculated value
                $data['shipping'] = $shippingResult['shipping_cost'] ?? ($data['shipping'] ?? 0);
                $context['shipping_breakdown'] = $shippingResult['breakdown'] ?? [];
                $context['product_shipping'] = $shippingResult['product_shipping'] ?? [];
                
                Log::info('CalculateShipping: Result', ['shipping' => $data['shipping'], 'product_shipping' => $context['product_shipping']]);
            } catch (\Exception $e) {
                Log::error('CalculateShipping: Error', ['error' => $e->getMessage()]);
                // If shipping calculation fails, keep the submitted value or use 0
                $data['shipping'] = $data['shipping'] ?? 0;
                $context['shipping_breakdown'] = [];
                $context['product_shipping'] = [];
            }
        } else {
            Log::warning('CalculateShipping: No customer address or city_id provided', [
                'has_customer_id' => isset($data['selected_customer_id']),
                'has_address_id' => isset($data['customer_address_id']),
                'has_city_id' => isset($data['external_city_id']),
            ]);
            // No shipping calculation needed, initialize empty arrays
            $context['shipping_breakdown'] = $context['shipping_breakdown'] ?? [];
            $context['product_shipping'] = $context['product_shipping'] ?? [];
        }

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }
}
