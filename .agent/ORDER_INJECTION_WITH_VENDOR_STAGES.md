# Order Injection with Vendor Stages - Implementation Notes

**Date:** February 2, 2026  
**Status:** Order Header Complete, Order Products Pending API Data

---

## System Architecture Understanding

### Order Structure
Your multi-vendor system has a sophisticated order structure:

```
orders (main order)
├── order_products (items in the order)
│   ├── product_id
│   ├── vendor_id (from product's brand/vendor)
│   ├── price, quantity, etc.
│   └── triggers OrderProductObserver on creation
│
└── vendor_order_stages (one per vendor in the order)
    ├── order_id
    ├── vendor_id
    ├── stage_id (vendor's fulfillment stage)
    ├── promo_code_share (vendor's share of promo discount)
    └── points_share (vendor's share of points discount)
```

### Automatic Vendor Stage Creation

**Key Insight:** Vendor order stages are created AUTOMATICALLY by the `OrderProductObserver` when order products are added!

```php
// From OrderProductObserver.php
public function created(OrderProduct $orderProduct): void
{
    $this->ensureVendorStageExists($orderProduct);
    // ...
}

protected function ensureVendorStageExists(OrderProduct $orderProduct): void
{
    // Check if vendor stage already exists
    $existingStage = VendorOrderStage::where('order_id', $orderProduct->order_id)
        ->where('vendor_id', $orderProduct->vendor_id)
        ->first();

    if ($existingStage) {
        return; // Already exists
    }

    // Get the default "new" stage
    $defaultStage = OrderStage::withoutGlobalScopes()
        ->where('type', 'new')
        ->first();

    // Create vendor order stage
    VendorOrderStage::create([
        'order_id' => $orderProduct->order_id,
        'vendor_id' => $orderProduct->vendor_id,
        'stage_id' => $defaultStage->id,
    ]);
}
```

**This means:**
- When you create an `OrderProduct`, the observer automatically creates a `VendorOrderStage` for that vendor
- Each vendor gets their own stage to track their fulfillment progress
- The stage starts as 'new' by default
- Discount shares (promo_code_share, points_share) are calculated and updated by `SyncOrderProducts` pipeline

---

## Current Implementation Status

### ✅ Implemented: Order Header Injection
- Creates main `orders` record
- Maps all order fields from old system
- Validates customer exists
- Sets default 'new' stage
- Handles create/update logic

### ❌ Not Yet Implemented: Order Products
**Reason:** API response shows empty data array

```json
{
  "data": {
    "orders": {
      "data": [],  // Empty!
      "total": 211
    }
  }
}
```

**What's needed:**
1. API must return order products data
2. Each product needs: product_id, vendor_id, quantity, price, etc.
3. Then we can create order_products records
4. Observer will automatically create vendor_order_stages

---

## How Vendor Stages Work

### 1. Order Creation Flow (Normal Checkout)
```
Customer places order
  ↓
CreateOrder pipeline creates order
  ↓
SyncOrderProducts pipeline creates order_products
  ↓
OrderProductObserver.created() fires
  ↓
ensureVendorStageExists() creates VendorOrderStage
  ↓
SyncOrderProducts updates discount shares
```

### 2. Discount Distribution
```php
// From SyncOrderProducts.php
foreach ($vendorTotals as $vendorId => $vendorTotal) {
    // Calculate vendor's proportion of the total order
    $vendorProportion = $grandTotal > 0 ? $vendorTotal / $grandTotal : 0;
    
    // Distribute discounts based on proportion
    $promoCodeShare = round($promoCodeDiscount * $vendorProportion, 2);
    $pointsShare = round($pointsCost * $vendorProportion, 2);
    
    VendorOrderStage::where('order_id', $order->id)
        ->where('vendor_id', $vendorId)
        ->update([
            'promo_code_share' => $promoCodeShare,
            'points_share' => $pointsShare,
        ]);
}
```

**Example:**
- Order total: $100
- Vendor A products: $60 (60%)
- Vendor B products: $40 (40%)
- Promo discount: $10
- Vendor A gets: $6 discount share
- Vendor B gets: $4 discount share

---

## What Needs to Happen for Full Order Injection

### Step 1: Get Order Products from API
The old system API needs to return order products data:

```json
{
  "data": {
    "orders": {
      "data": [
        {
          "id": 123,
          "customer_email": "john@example.com",
          "total_price": 260,
          "products": [  // ← This is what we need!
            {
              "product_id": 456,
              "vendor_product_id": 789,
              "vendor_id": 10,
              "quantity": 2,
              "price": 100,
              "tax": 10,
              "commission": 5,
              "shipping_cost": 10
            },
            {
              "product_id": 457,
              "vendor_product_id": 790,
              "vendor_id": 11,
              "quantity": 1,
              "price": 50,
              "tax": 5,
              "commission": 2.5,
              "shipping_cost": 5
            }
          ]
        }
      ]
    }
  }
}
```

### Step 2: Implement syncOrderProducts Method
```php
protected function syncOrderProducts($order, array $products): void
{
    foreach ($products as $productData) {
        // Validate product exists
        $product = \Modules\CatalogManagement\app\Models\Product::find($productData['product_id']);
        if (!$product) {
            Log::warning("Product not found", ['product_id' => $productData['product_id']]);
            continue;
        }

        // Create order product
        \Modules\Order\app\Models\OrderProduct::create([
            'order_id' => $order->id,
            'product_id' => $productData['product_id'],
            'vendor_product_id' => $productData['vendor_product_id'],
            'vendor_id' => $productData['vendor_id'],
            'quantity' => $productData['quantity'],
            'price' => $productData['price'],
            'tax' => $productData['tax'] ?? 0,
            'commission' => $productData['commission'] ?? 0,
            'shipping_cost' => $productData['shipping_cost'] ?? 0,
            // ... other fields
        ]);
        
        // OrderProductObserver will automatically create VendorOrderStage!
    }
}
```

### Step 3: Call syncOrderProducts in injectOrders
```php
// In injectOrders method, after creating order:
if (!empty($item['products'])) {
    $this->syncOrderProducts($order, $item['products']);
}
```

---

## Database Tables Involved

### orders
- Main order record
- Customer info, totals, stage_id
- One record per order

### order_products
- Line items in the order
- Links to products and vendors
- Multiple records per order
- **Triggers OrderProductObserver on insert**

### vendor_order_stages
- One record per vendor per order
- Tracks vendor's fulfillment stage
- Stores vendor's discount shares
- **Created automatically by OrderProductObserver**

### vendor_order_stage_histories
- Audit trail of stage changes
- Created by VendorOrderStageObserver
- Tracks who changed stage and when

---

## Testing Plan

### Phase 1: Order Headers Only (Current)
```bash
# Inject customers first
GET /en/eg/admin/inject-data?include=users

# Inject orders (headers only)
GET /en/eg/admin/inject-data?include=orders

# Verify
SELECT id, order_number, customer_email, total_price, stage_id
FROM orders
ORDER BY id DESC
LIMIT 10;
```

**Expected:** Orders created without products or vendor stages

### Phase 2: With Order Products (Future)
```bash
# After API returns products data
GET /en/eg/admin/inject-data?include=orders

# Verify orders
SELECT COUNT(*) FROM orders;

# Verify order products
SELECT COUNT(*) FROM order_products;

# Verify vendor stages (should be automatic!)
SELECT 
    vos.id,
    vos.order_id,
    vos.vendor_id,
    os.type as stage_type,
    vos.promo_code_share,
    vos.points_share
FROM vendor_order_stages vos
JOIN order_stages os ON vos.stage_id = os.id
ORDER BY vos.order_id DESC
LIMIT 10;
```

**Expected:** 
- Orders created
- Order products created
- Vendor stages created automatically (one per vendor per order)
- Discount shares calculated

---

## Key Takeaways

1. **Vendor stages are automatic** - Don't manually create them, the observer does it
2. **Order products trigger the observer** - Create products, get stages for free
3. **Discount shares are calculated** - SyncOrderProducts pipeline handles this
4. **API needs product data** - Current response has empty data array
5. **Products must exist first** - Inject products before orders

---

## Next Steps

1. ✅ Order header injection is complete
2. ⏳ Wait for API to return order products data
3. ⏳ Implement `syncOrderProducts()` method
4. ⏳ Test full order injection with products
5. ⏳ Verify vendor stages are created automatically
6. ⏳ Verify discount shares are calculated correctly

---

## Status Summary

| Component | Status | Notes |
|-----------|--------|-------|
| Order headers | ✅ Complete | Creates main order record |
| Customer validation | ✅ Complete | Skips if customer not found |
| Stage assignment | ✅ Complete | Uses 'new' stage dynamically |
| Order products | ❌ Pending | Waiting for API data |
| Vendor stages | ⏳ Automatic | Will be created by observer |
| Discount shares | ⏳ Automatic | Will be calculated by pipeline |

---

**Conclusion:** The order injection foundation is solid. Once the API provides order products data, the rest will fall into place automatically thanks to the observer pattern!
