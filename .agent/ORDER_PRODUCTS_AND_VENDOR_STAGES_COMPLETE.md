# Order Products and Vendor Stages Injection - COMPLETE ✅

**Status:** COMPLETE  
**Date:** February 2, 2026  
**File:** `app/Http/Controllers/Api/InjectDataController.php`

---

## Overview
Implemented complete order injection including order products and vendor order stages. Vendor stages are set to the same stage as the main order.

---

## Implementation Details

### 1. Order Injection Flow
```
1. Create main order record
   ↓
2. If products provided in API:
   ├── Create order_products records
   ├── Disable OrderProductObserver (to prevent automatic vendor stage creation)
   ├── Track unique vendor IDs
   └── Store product translations
   ↓
3. Create vendor_order_stages
   ├── One record per vendor
   ├── stage_id = same as order stage_id
   └── promo_code_share & points_share = 0 (can be calculated later)
```

### 2. Key Features

#### A. Order Products Creation ✅
```php
protected function syncOrderProducts($order, array $products, int $stageId): void
{
    foreach ($products as $productData) {
        // Validate vendor product exists
        $vendorProduct = VendorProduct::withoutGlobalScopes()
            ->find($vendorProductId);
        
        if (!$vendorProduct) {
            continue; // Skip if not found
        }
        
        // Create order product WITHOUT triggering observer
        $orderProduct = new OrderProduct();
        // ... set fields ...
        
        OrderProduct::unsetEventDispatcher(); // Disable observer
        $orderProduct->save();
        OrderProduct::setEventDispatcher(new Dispatcher()); // Re-enable
        
        // Track vendor IDs
        $vendorIds[] = $vendorId;
    }
}
```

**Why disable observer?**
- Prevents automatic vendor stage creation
- We create vendor stages manually with correct stage_id
- Avoids duplicate vendor stages

#### B. Vendor Stages Creation ✅
```php
// Create vendor order stages (one per vendor)
foreach ($vendorIds as $vendorId) {
    VendorOrderStage::create([
        'order_id' => $order->id,
        'vendor_id' => $vendorId,
        'stage_id' => $stageId, // Same as order stage!
        'promo_code_share' => 0,
        'points_share' => 0,
    ]);
}
```

**Key Points:**
- One vendor stage per vendor per order
- `stage_id` matches the order's `stage_id`
- All vendors start with same stage
- Discount shares set to 0 (can be calculated if needed)

#### C. Product Validation ✅
- Checks if `vendor_product_id` exists
- Checks if `vendor_id` is provided
- Skips products that don't exist in database
- Logs warnings for skipped products

#### D. Translation Support ✅
```php
if (!empty($productData['name_en'])) {
    $orderProduct->setTranslation('name', 'en', $productData['name_en']);
}
if (!empty($productData['name_ar'])) {
    $orderProduct->setTranslation('name', 'ar', $productData['name_ar']);
}
```

---

## API Response Structure Expected

### Order with Products
```json
{
  "status": true,
  "message": "ok",
  "data": {
    "orders": {
      "data": [
        {
          "id": 123,
          "customer_email": "john@example.com",
          "customer_name": "John Doe",
          "total_price": 260,
          "shipping": 50,
          "total_tax": 15,
          "items_count": 3,
          "created_at": "01 Feb, 2026, 10:00 AM",
          "products": [
            {
              "vendor_product_id": 789,
              "vendor_product_variant_id": 1001,
              "vendor_id": 10,
              "quantity": 2,
              "price": 100,
              "commission": 5,
              "shipping_cost": 25,
              "name_en": "Product Name",
              "name_ar": "اسم المنتج"
            },
            {
              "vendor_product_id": 790,
              "vendor_product_variant_id": null,
              "vendor_id": 11,
              "quantity": 1,
              "price": 50,
              "commission": 2.5,
              "shipping_cost": 25,
              "name_en": "Another Product",
              "name_ar": "منتج آخر"
            }
          ]
        }
      ],
      "per_page": 10,
      "total": 211
    }
  }
}
```

### Required Product Fields
| Field | Required | Description |
|-------|----------|-------------|
| `vendor_product_id` | ✅ Yes | ID of vendor product |
| `vendor_id` | ✅ Yes | ID of vendor/brand |
| `quantity` | ✅ Yes | Quantity ordered |
| `price` | ✅ Yes | Product price |
| `vendor_product_variant_id` | ❌ No | Variant ID if applicable |
| `commission` | ❌ No | Commission amount (default: 0) |
| `shipping_cost` | ❌ No | Shipping cost (default: 0) |
| `name_en` | ❌ No | Product name in English |
| `name_ar` | ❌ No | Product name in Arabic |
| `occasion_id` | ❌ No | Occasion ID if applicable |
| `bundle_id` | ❌ No | Bundle ID if applicable |

---

## Database Structure

### orders
```sql
CREATE TABLE orders (
    id BIGINT PRIMARY KEY,
    customer_id BIGINT,
    customer_name VARCHAR(255),
    customer_email VARCHAR(255),
    total_price DECIMAL(10,2),
    stage_id BIGINT, -- Main order stage
    -- ... other fields
);
```

### order_products
```sql
CREATE TABLE order_products (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    order_id BIGINT,
    vendor_product_id BIGINT,
    vendor_product_variant_id BIGINT NULL,
    vendor_id BIGINT,
    quantity INT,
    price DECIMAL(10,2),
    commission DECIMAL(10,2),
    shipping_cost DECIMAL(10,2),
    stage_id BIGINT, -- Product stage (same as order)
    -- ... other fields
);
```

### vendor_order_stages
```sql
CREATE TABLE vendor_order_stages (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    order_id BIGINT,
    vendor_id BIGINT,
    stage_id BIGINT, -- Vendor's stage (same as order)
    promo_code_share DECIMAL(10,2),
    points_share DECIMAL(10,2),
    -- ... other fields
);
```

---

## Usage

### Inject Orders with Products
```bash
GET /en/eg/admin/inject-data?include=orders
```

**Prerequisites:**
1. ✅ Customers must be injected first
2. ✅ Vendor products must exist in database
3. ✅ Order stages must be seeded

### Inject Specific Pages
```bash
GET /en/eg/admin/inject-data?include=orders&page=1&limit_pages=5
```

---

## Response Example

### Success Response
```json
{
  "status": true,
  "message": "Data injected successfully",
  "total_fetched": 211,
  "pages_processed": 22,
  "last_page": 22,
  "result": {
    "type": "orders",
    "injected": 200,
    "updated": 0,
    "skipped": 11,
    "errors": [
      "Order 45: Customer not found (email: missing@example.com)"
    ]
  }
}
```

### Logs
```
[INFO] Processing Order: id=123, customer_email=john@example.com
[INFO] Order CREATED: id=123
[INFO] Order products synced: order_id=123, products_created=3, vendors_count=2
[INFO] Vendor order stage created: order_id=123, vendor_id=10, stage_id=1
[INFO] Vendor order stage created: order_id=123, vendor_id=11, stage_id=1
```

---

## Verification Queries

### Check Orders
```sql
SELECT 
    o.id,
    o.order_number,
    o.customer_email,
    o.total_price,
    o.items_count,
    os.type as stage_type
FROM orders o
JOIN order_stages os ON o.stage_id = os.id
ORDER BY o.id DESC
LIMIT 10;
```

### Check Order Products
```sql
SELECT 
    op.id,
    op.order_id,
    op.vendor_product_id,
    op.vendor_id,
    op.quantity,
    op.price,
    op.commission
FROM order_products op
WHERE op.order_id = 123;
```

### Check Vendor Stages
```sql
SELECT 
    vos.id,
    vos.order_id,
    vos.vendor_id,
    os.type as stage_type,
    vos.promo_code_share,
    vos.points_share
FROM vendor_order_stages vos
JOIN order_stages os ON vos.stage_id = os.id
WHERE vos.order_id = 123;
```

### Verify Stage Consistency
```sql
-- All vendor stages should have same stage_id as order
SELECT 
    o.id as order_id,
    o.stage_id as order_stage_id,
    vos.vendor_id,
    vos.stage_id as vendor_stage_id,
    CASE 
        WHEN o.stage_id = vos.stage_id THEN 'OK'
        ELSE 'MISMATCH'
    END as status
FROM orders o
JOIN vendor_order_stages vos ON o.id = vos.order_id
WHERE o.id = 123;
```

**Expected:** All rows should show `status = 'OK'`

---

## Important Notes

### 1. Observer Disabled During Injection
```php
OrderProduct::unsetEventDispatcher(); // Disable
$orderProduct->save();
OrderProduct::setEventDispatcher(new Dispatcher()); // Re-enable
```

**Why?**
- `OrderProductObserver` automatically creates vendor stages
- We want to create them manually with correct stage_id
- Prevents duplicate vendor stages

### 2. Stage Consistency
- Order has `stage_id`
- All order products have same `stage_id`
- All vendor stages have same `stage_id`
- **All stages are synchronized!**

### 3. Discount Shares
- Set to 0 during injection
- Can be calculated later if needed
- Formula: `vendor_share = (vendor_total / order_total) * discount`

### 4. Product Validation
- Skips products if `vendor_product_id` not found
- Skips products if `vendor_id` missing
- Logs warnings for debugging
- Continues with other products

---

## Testing Checklist

- [x] Order header created
- [x] Order products created
- [x] Vendor stages created
- [x] Stage IDs match across order/products/vendor stages
- [x] Product translations stored
- [x] Observer disabled during injection
- [x] Vendor IDs tracked correctly
- [x] Duplicate vendor stages prevented
- [x] Error handling and logging
- [x] Skipped products logged

---

## Troubleshooting

### Issue: No products created
**Check:**
1. API returns `products` array in order data
2. `vendor_product_id` exists in database
3. Check logs for "Order product skipped" warnings

### Issue: No vendor stages created
**Check:**
1. Products were created successfully
2. `vendor_id` is provided in product data
3. Check logs for "Vendor order stage created" messages

### Issue: Stage mismatch
**Check:**
1. Verify order `stage_id`
2. Run stage consistency query above
3. Check if observer was properly disabled

---

## Files Modified

**File:** `app/Http/Controllers/Api/InjectDataController.php`

**Changes:**
1. Updated `injectOrders()` to call `syncOrderProducts()`
2. Added `syncOrderProducts()` method
3. Implemented product validation
4. Implemented vendor stage creation
5. Added observer disabling logic
6. Added translation support

---

## Status: COMPLETE ✅

Order injection is fully implemented with:
- ✅ Order headers
- ✅ Order products
- ✅ Vendor order stages
- ✅ Stage consistency (all same stage)
- ✅ Product validation
- ✅ Translation support
- ✅ Error handling

Ready for production use!
