<?php

namespace Modules\Order\app\Pipelines;

use Closure;
use App\Exceptions\OrderException;
use Modules\CatalogManagement\app\Services\Api\ProductApiService;

class CalculateApiProductPrices
{
    public function __construct(
        private ProductApiService $productService,
    ) {}

    /**
     * Handle the pipeline for API checkout.
     *
     * Fetches complete product data from service and prepares data for OrderProduct table.
     * Handles product, bundle, and occasion types with appropriate pricing.
     */
    public function handle($payload, Closure $next)
    {
        $data = $payload['data'];
        $context = $payload['context'];

        $products = $context['products'];
        $totalProductPrice = 0;
        $totalTax = 0;
        $totalCommission = 0;
        $itemsCount = 0;
        $productsData = [];
        $productSalesData = [];

        foreach ($products as $formProduct) {
            $vendorProductId = $formProduct['vendor_product_id'];
            $vendorProductVariantId = $formProduct['vendor_product_variant_id'] ?? null;
            $quantity = (int) $formProduct['quantity'];
            $type = $formProduct['type'] ?? 'product';
            $bundleId = $formProduct['bundle_id'] ?? null;
            $occasionId = $formProduct['occasion_id'] ?? null;

            // Determine price based on type FIRST (using cart data which has occasion/bundle pricing)
            $price = $this->getPriceFromCart($type, $formProduct);

            // Get product details from service with all relationships for order creation
            $vendorProduct = $this->productService->findProductForOrder($vendorProductId);

            // Validate product data exists
            if (!$vendorProduct || !isset($vendorProduct['product'])) {
                throw new OrderException(trans('order::order.product_not_found', ['id' => $vendorProductId]));
            }

            // Extract necessary data
            $productId = $vendorProduct['product']['id'] ?? null;
            if (!$productId) {
                throw new OrderException(trans('order::order.product_id_not_found', ['id' => $vendorProductId]));
            }

            $productNameEn = $vendorProduct['product']['title_en'] ?? $vendorProduct['product']['title'] ?? 'Unknown Product';
            $productNameAr = $vendorProduct['product']['title_ar'] ?? $vendorProduct['product']['title'] ?? 'Unknown Product';
            $vendorId = $vendorProduct['vendor']['id'] ?? null;
            if (!$vendorId) {
                throw new OrderException(trans('order::order.vendor_id_not_found', ['id' => $vendorProductId]));
            }

            // If price wasn't determined from cart, use variant price as fallback
            if (!$price) {
                $price = (float) ($vendorProduct['variants'][0]['price'] ?? 0);
            }

            $taxRate = (float) ($vendorProduct['tax']['tax_rate'] ?? 0);
            $taxNameEn = $vendorProduct['tax']['name_en'] ?? $vendorProduct['tax']['name'] ?? '';
            $taxNameAr = $vendorProduct['tax']['name_ar'] ?? $vendorProduct['tax']['name'] ?? '';
            $limitation = (int) ($vendorProduct['max_per_order'] ?? 0);

            // Calculate total commission from all vendor activities
            $totalCommissionRate = 0;
            if (isset($vendorProduct['vendor']['activities']) && is_array($vendorProduct['vendor']['activities'])) {
                foreach ($vendorProduct['vendor']['activities'] as $activity) {
                    $totalCommissionRate += (float) ($activity['commission'] ?? 0);
                }
            }

            // Calculate totals
            $productTotal = $price * $quantity;
            $tax = ($productTotal * $taxRate) / 100;
            $commissionAmount = ($productTotal * $totalCommissionRate) / 100;

            $totalProductPrice += $productTotal;
            $totalTax += $tax;
            $totalCommission += $commissionAmount;
            $itemsCount += $quantity;

            $productsData[] = [
                'vendor_product_id' => $vendorProductId,
                'vendor_product_variant_id' => $vendorProductVariantId,
                'vendor_id' => $vendorId,
                'quantity' => $quantity,
                'price' => $price,
                'commission' => $commissionAmount,
                'type' => $type,
                'bundle_id' => $bundleId,
                'occasion_id' => $occasionId,
                'translations' => [
                    'en' => [
                        'name' => $productNameEn,
                    ],
                    'ar' => [
                        'name' => $productNameAr,
                    ],
                ],
                'tax_id' => $vendorProduct['tax']['id'] ?? null,
                'tax_rate' => $taxRate,
                'tax_amount' => $tax,
                'tax_translations' => [
                    'en' => $taxNameEn,
                    'ar' => $taxNameAr,
                ],
                'total' => $productTotal,
                'limitation' => $limitation,
            ];

            $productSalesData[$vendorProductId] = $quantity;
        }

        $context['products_data'] = $productsData;
        $context['total_product_price'] = $totalProductPrice;
        $context['total_tax'] = $totalTax;
        $context['total_commission'] = $totalCommission;
        $context['items_count'] = $itemsCount;
        $context['product_sales_to_update'] = $productSalesData;

        return $next([
            'data' => $data,
            'context' => $context,
        ]);
    }

    /**
     * Get price from cart data (occasion/bundle pricing)
     * Returns 0 if not found, allowing fallback to variant price
     */
    private function getPriceFromCart(string $type, array $formProduct): float
    {
        if ($type === 'bundle' && isset($formProduct['bundle'])) {
            $bundle = $formProduct['bundle'];
            if ($bundle && isset($bundle['bundleProducts'])) {
                $bundleProduct = collect($bundle['bundleProducts'])
                    ->firstWhere('vendor_product_variant_id', $formProduct['vendor_product_variant_id']);

                if ($bundleProduct && isset($bundleProduct['price'])) {
                    return (float) $bundleProduct['price'];
                }
            }
        }

        if ($type === 'occasion' && isset($formProduct['occasion'])) {
            $occasion = $formProduct['occasion'];
            if ($occasion && isset($occasion['occasionProducts'])) {
                $occasionProduct = collect($occasion['occasionProducts'])
                    ->firstWhere('vendor_product_variant_id', $formProduct['vendor_product_variant_id']);

                if ($occasionProduct && isset($occasionProduct['special_price'])) {
                    return (float) $occasionProduct['special_price'];
                }
            }
        }

        // Return 0 to indicate price not found in cart data
        return 0;
    }

    /**
     * Get price based on product type (product, bundle, or occasion)
     */
    private function getPrice(string $type, array $formProduct, array $vendorProduct): float
    {
        // Default to variant price
        $variantPrice = (float) ($vendorProduct['variants'][0]['price'] ?? 0);

        if ($type === 'bundle' && isset($formProduct['bundle'])) {
            $bundle = $formProduct['bundle'];
            if ($bundle && isset($bundle['bundleProducts'])) {
                $bundleProduct = collect($bundle['bundleProducts'])
                    ->firstWhere('vendor_product_variant_id', $formProduct['vendor_product_variant_id']);

                if ($bundleProduct && isset($bundleProduct['price'])) {
                    return (float) $bundleProduct['price'];
                }
            }
        }

        if ($type === 'occasion' && isset($formProduct['occasion'])) {
            $occasion = $formProduct['occasion'];
            if ($occasion && isset($occasion['occasionProducts'])) {
                $occasionProduct = collect($occasion['occasionProducts'])
                    ->firstWhere('vendor_product_variant_id', $formProduct['vendor_product_variant_id']);

                if ($occasionProduct && isset($occasionProduct['special_price'])) {
                    return (float) $occasionProduct['special_price'];
                }
            }
        }

        // Fallback to variant price
        return $variantPrice;
    }
}
