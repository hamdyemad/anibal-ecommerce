<?php

namespace Modules\Order\app\Repositories;

use Modules\Order\app\Interfaces\OrderFulfillmentRepositoryInterface;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderFulfillment;
use Modules\Order\app\Models\OrderProduct;
use Modules\AreaSettings\app\Models\Region;
use Modules\CatalogManagement\app\Models\StockBooking;
use Illuminate\Support\Facades\DB;

class OrderFulfillmentRepository implements OrderFulfillmentRepositoryInterface
{
    /**
     * Get fulfillments for an order
     */
    public function getByOrder($orderId)
    {
        return OrderFulfillment::where('order_id', $orderId)->get();
    }

    /**
     * Get fulfillments with order products and regions
     */
    public function getByOrderWithRelations($orderId)
    {
        return OrderFulfillment::where('order_id', $orderId)
            ->with(['orderProduct', 'region'])
            ->get()
            ->groupBy('order_product_id');
    }

    /**
     * Create or update fulfillment
     */
    public function createOrUpdate($orderId, $orderProductId, $regionId, $quantity)
    {
        if ($quantity > 0) {
            return OrderFulfillment::updateOrCreate(
                [
                    'order_product_id' => $orderProductId,
                    'region_id' => $regionId,
                ],
                [
                    'order_id' => $orderId,
                    'allocated_quantity' => $quantity,
                ]
            );
        } else {
            // Delete if quantity is 0
            OrderFulfillment::where('order_product_id', $orderProductId)
                ->where('region_id', $regionId)
                ->delete();
        }
    }

    /**
     * Delete fulfillment
     */
    public function delete($orderProductId, $regionId)
    {
        return OrderFulfillment::where('order_product_id', $orderProductId)
            ->where('region_id', $regionId)
            ->delete();
    }

    /**
     * Get stock data for order
     */
    public function getStockDataForOrder($orderId)
    {
        $order = Order::with([
            'products.vendorProduct.product',
            'products.vendorProduct.vendor',
            'products.vendorProductVariant.variantConfiguration.key',
            'products.stage' => function($query) {
                $query->withoutGlobalScopes(); // Load stages without global scopes
            }
        ])->findOrFail($orderId);

        // Filter products based on user role
        $products = $order->products;
        
        \Log::info('=== ALLOCATE PAGE DEBUG ===');
        \Log::info('Order ID: ' . $orderId);
        \Log::info('Total products in order: ' . $products->count());
        \Log::info('Is Admin: ' . (isAdmin() ? 'YES' : 'NO'));
        
        // Log all products with their details
        foreach ($products as $product) {
            \Log::info('Product #' . $product->id . ':', [
                'name' => $product->getTranslation('name', app()->getLocale()) ?? 'N/A',
                'vendor_id' => $product->vendor_id,
                'stage_id' => $product->stage_id,
                'stage_type' => $product->stage?->type,
                'stage_slug' => $product->stage?->slug,
                'stage_name' => $product->stage?->getTranslation('name', app()->getLocale()) ?? 'N/A',
            ]);
        }
        
        // If vendor, only show their products
        if (!isAdmin()) {
            $currentVendorId = auth()->user()->vendor?->id;
            \Log::info('Current Vendor ID: ' . $currentVendorId);
            
            if ($currentVendorId) {
                $beforeCount = $products->count();
                $products = $products->filter(function($product) use ($currentVendorId) {
                    $matches = $product->vendor_id == $currentVendorId;
                    \Log::info('Vendor filter - Product #' . $product->id . ': vendor_id=' . $product->vendor_id . ', current=' . $currentVendorId . ', matches=' . ($matches ? 'YES' : 'NO'));
                    return $matches;
                });
                
                \Log::info('After vendor filter: ' . $beforeCount . ' -> ' . $products->count());
            }
        }
        
        // Only show products with "in_progress" stage
        $beforeStageFilter = $products->count();
        $products = $products->filter(function($product) {
            // Check both type and slug for "in_progress" or "in-progress"
            $isInProgress = $product->stage && (
                $product->stage->type === 'in_progress' || 
                $product->stage->slug === 'in-progress' ||
                $product->stage->slug === 'in_progress'
            );
            
            \Log::info('Stage filter - Product #' . $product->id . ': type=' . ($product->stage?->type ?? 'NULL') . ', slug=' . ($product->stage?->slug ?? 'NULL') . ', matches=' . ($isInProgress ? 'YES' : 'NO'));
            
            return $isInProgress;
        });
        
        \Log::info('After stage filter: ' . $beforeStageFilter . ' -> ' . $products->count());
        \Log::info('Final products to show:', $products->pluck('id')->toArray());
        \Log::info('=== END DEBUG ===');

        $regions = Region::with('stocks')->get();
        $existingFulfillments = $this->getByOrderWithRelations($orderId);

        $stockData = [];

        foreach ($products as $orderProduct) {
            $vendorProductVariant = $orderProduct->vendorProductVariant;

            // Check if product is fully allocated
            $totalAllocatedForProduct = OrderFulfillment::where('order_product_id', $orderProduct->id)
                ->sum('allocated_quantity');
            $isFullyAllocated = $totalAllocatedForProduct >= $orderProduct->quantity;

            // Get booking quantity for this product
            $bookingQuantity = \Modules\CatalogManagement\app\Models\StockBooking::where('order_id', $orderId)
                ->where('order_product_id', $orderProduct->id)
                ->sum('booked_quantity');

            $stockData[$orderProduct->id] = [
                'order_product' => $orderProduct,
                'vendor_product_variant' => $vendorProductVariant,
                'is_fully_allocated' => $isFullyAllocated,
                'total_allocated' => $totalAllocatedForProduct,
                'booking_quantity' => $bookingQuantity,
                'regions' => []
            ];

            foreach ($regions as $region) {
                // Calculate allocated from OrderFulfillment
                $allocatedQuantity = $existingFulfillments->get($orderProduct->id, collect())
                    ->where('region_id', $region->id)
                    ->sum('allocated_quantity');

                // Get total stock for this variant in this region
                $variantStock = $region->stocks()
                    ->where('vendor_product_variant_id', $orderProduct->vendor_product_variant_id)
                    ->first();
                $totalStock = $variantStock ? $variantStock->quantity : 0;

                // Get booking for current order in this region (only if not yet allocated)
                $bookingInRegion = \Modules\CatalogManagement\app\Models\StockBooking::where('order_id', $orderId)
                    ->where('order_product_id', $orderProduct->id)
                    ->where('region_id', $region->id)
                    ->where('status', \Modules\CatalogManagement\app\Models\StockBooking::STATUS_BOOKED) // Only show 'booked' status, not 'allocated'
                    ->sum('booked_quantity');

                // Get already allocated stock for this product in this region (from other allocations)
                $alreadyAllocatedInRegion = OrderFulfillment::where('order_product_id', $orderProduct->id)
                    ->where('region_id', $region->id)
                    ->sum('allocated_quantity');

                // Get booked stock EXCLUDING the current order's booking
                // (because we're allocating this order, so its booking should be available)
                $bookedStock = \Modules\CatalogManagement\app\Models\StockBooking::where('vendor_product_variant_id', $orderProduct->vendor_product_variant_id)
                    ->where('region_id', $region->id)
                    ->where('status', \Modules\CatalogManagement\app\Models\StockBooking::STATUS_BOOKED)
                    ->where('order_id', '!=', $orderId) // Exclude current order's booking
                    ->sum('booked_quantity');

                // Available stock = Total - Other orders' bookings
                $availableStock = max(0, $totalStock - $bookedStock);

                $stockData[$orderProduct->id]['regions'][$region->id] = [
                    'region' => $region,
                    'total_stock' => $totalStock,
                    'booking_quantity' => $bookingInRegion,
                    'already_allocated' => $alreadyAllocatedInRegion,
                    'available_stock' => $availableStock,
                    'allocated_quantity' => $allocatedQuantity,
                    'remaining_stock' => $availableStock - $allocatedQuantity
                ];
            }
        }

        return [
            'order' => $order,
            'stockData' => $stockData,
            'regions' => $regions,
        ];
    }

    /**
     * Save allocations
     */
    public function saveAllocations($orderId, $allocations)
    {
        DB::beginTransaction();
        try {
            foreach ($allocations as $allocation) {
                $this->createOrUpdate(
                    $orderId,
                    $allocation['order_product_id'],
                    $allocation['region_id'],
                    $allocation['quantity']
                );
            }

            // Validate allocations
            $this->validateAllocations($orderId);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Validate allocations
     */
    public function validateAllocations($orderId)
    {
        $order = Order::with([
            'products.stage' => function($query) {
                $query->withoutGlobalScopes();
            }
        ])->findOrFail($orderId);

        // Only validate products that are in "in_progress" stage
        $productsToValidate = $order->products->filter(function($product) {
            return $product->stage && (
                $product->stage->type === 'in_progress' || 
                $product->stage->slug === 'in-progress' ||
                $product->stage->slug === 'in_progress'
            );
        });

        // If vendor, only validate their products
        if (!isAdmin()) {
            $currentVendorId = auth()->user()->vendor?->id;
            if ($currentVendorId) {
                $productsToValidate = $productsToValidate->filter(function($product) use ($currentVendorId) {
                    return $product->vendor_id == $currentVendorId;
                });
            }
        }

        foreach ($productsToValidate as $orderProduct) {
            $totalAllocated = OrderFulfillment::where('order_product_id', $orderProduct->id)
                ->sum('allocated_quantity');

            if ($totalAllocated != $orderProduct->quantity) {
                throw new \Exception(
                    "Total allocated quantity ({$totalAllocated}) for product '{$orderProduct->getTranslation('name', app()->getLocale())}' must equal ordered quantity ({$orderProduct->quantity})"
                );
            }
        }
    }

    /**
     * Update stock regions after fulfillment
     */
    public function updateStockRegions($orderId)
    {
        $fulfillments = OrderFulfillment::where('order_id', $orderId)->get();

        foreach ($fulfillments as $fulfillment) {
            // Update stock booking with allocated region and status (don't decrement actual stock)
            $stockBooking = StockBooking::where('order_id', $orderId)
                ->where('order_product_id', $fulfillment->order_product_id)
                ->first();

            if ($stockBooking) {
                $stockBooking->update([
                    'allocated_region_id' => $fulfillment->region_id,
                    'status' => StockBooking::STATUS_ALLOCATED,
                    'allocated_at' => now(),
                ]);
            }
        }
    }
}
