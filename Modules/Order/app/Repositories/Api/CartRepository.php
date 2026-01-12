<?php

namespace Modules\Order\app\Repositories\Api;

use App\Actions\IsPaginatedAction;
use Modules\Order\app\Actions\CartQueryAction;
use Modules\Order\app\Interfaces\Api\CartRepositoryInterface;
use Modules\Order\app\Models\Cart;
use Illuminate\Support\Facades\DB;

class CartRepository implements CartRepositoryInterface
{
    public function __construct(protected CartQueryAction $query, protected IsPaginatedAction $paginated)
    {}

    /**
     * Get all cart items for a customer with pagination
     */
    public function getCustomerCart(array $data, $customerId)
    {
        $query = $this->query->handle($customerId, $data);
        $result = $this->paginated->handle($query, $data['per_page'] ?? 15, $data['paginated'] ?? false);
        return $result;
    }

    public function addToCart($customerId, array $itemData)
    {
        return DB::transaction(function () use ($customerId, $itemData) {
            $existingCart = Cart::where('customer_id', $customerId)
                ->where('vendor_product_id', $itemData['vendor_product_id'])
                ->where('vendor_product_variant_id', $itemData['vendor_product_variant_id'] ?? null)
                ->where('type', $itemData['type'] ?? 'product')
                ->where('bundle_id', $itemData['bundle_id'] ?? null)
                ->where('occasion_id', $itemData['occasion_id'] ?? null)
                ->first();

            if ($existingCart) {
                $existingCart->update([
                    'quantity' => $itemData['quantity'],
                ]);
                return $existingCart;
            }

            return Cart::create([
                'customer_id' => $customerId,
                'vendor_product_id' => $itemData['vendor_product_id'],
                'vendor_product_variant_id' => $itemData['vendor_product_variant_id'] ?? null,
                'type' => $itemData['type'] ?? 'product',
                'bundle_id' => $itemData['bundle_id'] ?? null,
                'occasion_id' => $itemData['occasion_id'] ?? null,
                'quantity' => $itemData['quantity'] ?? 1,
            ]);
        });
    }

    /**
     * Add bundle with multiple items to cart
     */
    public function addBulkToCart($customerId, array $items)
    {
        return DB::transaction(function () use ($customerId, $items) {
            $cartItems = [];
            foreach ($items as $item) {
                $cartItem = $this->addToCart($customerId, [
                    'vendor_product_id' => $item['vendor_product_id'],
                    'vendor_product_variant_id' => $item['vendor_product_variant_id'],
                    'quantity' => $item['quantity'],
                    'type' => $item["type"],
                    'bundle_id' => $item['bundle_id'] ?? null,
                    'occasion_id' => $item['occasion_id'] ?? null,
                ]);
                $cartItems[] = $cartItem;
            }
            return $cartItems;
        });
    }

    /**
     * Remove a product from cart
     */
    public function removeFromCart($customerId, $cartItemId)
    {
        return DB::transaction(function () use ($customerId, $cartItemId) {
            $cartItem = Cart::where('customer_id', $customerId)->findOrFail($cartItemId);
            return $cartItem->delete();
        });
    }

    /**
     * Remove all items from cart
     */
    public function clearCart($customerId)
    {
        return DB::transaction(function () use ($customerId) {
            return Cart::where('customer_id', $customerId)->delete();
        });
    }

    /**
     * Check if product is in cart
     */
    public function isInCart($customerId, $vendorProductId, $vendorProductVariantId, $type = 'product', $bundleId = null, $occasionId = null): bool
    {
        return Cart::isInCart($customerId, $vendorProductId, $vendorProductVariantId, $type, $bundleId, $occasionId);
    }

    /**
     * Get cart count for customer (total quantity)
     */
    public function getCartCount($customerId): int
    {
        return Cart::getTotalItems($customerId);
    }

    /**
     * Get cart summary with totals
     * Note: Prices in database are BEFORE tax, so we calculate tax and add it
     */
    public function getCartSummary($customerId)
    {
        $carts = $this->query->handle($customerId)->get();

        if ($carts->isEmpty()) {
            return null;
        }

        $totalPriceWithTax = 0;
        $totalTaxAmount = 0;
        $totalProductPrice = 0;

        foreach ($carts as $cart) {
            // Get base price (before tax) from database
            $lineItemTotalBeforeTax = $this->calculateLineItemTotal($cart);
            
            // Calculate tax and price with tax
            if ($cart->vendorProduct && $cart->vendorProduct->taxes) {
                $taxRate = $cart->vendorProduct->taxes->sum('percentage');
                $taxAmount = $lineItemTotalBeforeTax * ($taxRate / 100);
                $lineItemTotalWithTax = $lineItemTotalBeforeTax + $taxAmount;
                
                $totalTaxAmount += $taxAmount;
                $totalProductPrice += $lineItemTotalBeforeTax;
                $totalPriceWithTax += $lineItemTotalWithTax;
            } else {
                // No tax, so the full amount is product price
                $totalProductPrice += $lineItemTotalBeforeTax;
                $totalPriceWithTax += $lineItemTotalBeforeTax;
            }
        }

        return [
            'totalProductPrice' => round($totalProductPrice, 2),
            'totalTaxAmount' => round($totalTaxAmount, 2),
            'finalTotalPrice' => round($totalPriceWithTax, 2),
        ];
    }

    /**
     * Calculate line item total based on cart type
     * For bundles: if quantity exceeds limitation_quantity, extra items use original variant price
     */
    private function calculateLineItemTotal($cart)
    {
        if ($cart->type === 'bundle' && $cart->bundle_id) {
            // Query database directly for bundle product price
            $bundleProduct = \Modules\CatalogManagement\app\Models\BundleProduct::where('bundle_id', $cart->bundle_id)
                ->where('vendor_product_variant_id', $cart->vendor_product_variant_id)
                ->first();

            if ($bundleProduct) {
                $bundlePrice = (float) $bundleProduct->price;
                $limitQty = $bundleProduct->limitation_quantity ?? $cart->quantity;
                $cartQty = $cart->quantity;
                
                // If quantity is within limit, use bundle price for all
                if ($cartQty <= $limitQty) {
                    return $bundlePrice * $cartQty;
                }
                
                // Quantity exceeds limit: bundle price for limit, original price for extra
                $bundleTotal = $bundlePrice * $limitQty;
                $extraQty = $cartQty - $limitQty;
                
                // Get original variant price for extra items
                $originalPrice = 0;
                if ($cart->vendorProductVariant) {
                    $originalPrice = (float) ($cart->vendorProductVariant->price ?? 0);
                }
                $extraTotal = $originalPrice * $extraQty;
                
                return $bundleTotal + $extraTotal;
            }
        }
        
        if ($cart->type === 'occasion' && $cart->occasion_id) {
            // Query database directly for occasion product price
            $occasionProduct = \Modules\CatalogManagement\app\Models\OccasionProduct::where('occasion_id', $cart->occasion_id)
                ->where('vendor_product_variant_id', $cart->vendor_product_variant_id)
                ->first();

            if ($occasionProduct) {
                return (float) $occasionProduct->special_price * $cart->quantity;
            }
        }
        
        // Default: regular product price
        if ($cart->vendorProductVariant) {
            $price = $cart->vendorProductVariant->price ?? 0;
            return (float) $price * $cart->quantity;
        }

        return 0;
    }
}
