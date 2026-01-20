# Refund Completion & Withdraw System Integration

## Overview
تم تحديث نظام الاسترجاع ليتكامل بشكل كامل مع نظام السحب (Withdraws) والمحاسبة (Accounting).

## What Happens When Refund is Completed (status = 'refunded')

### 1. Customer Points Management ✅
- **Deduct earned points**: إذا حصل العميل على نقاط من هذا الطلب، يتم خصمها
- **Return used points**: إذا استخدم العميل نقاط في الشراء، يتم إرجاعها

### 2. Order Update ✅
- تحديث `refunded_amount` في جدول `orders` لتتبع إجمالي المبالغ المستردة

### 3. Stock Management ✅
- استخدام `StockBookingService` لإرجاع المنتجات للمخزون

### 4. Accounting Entry ✅
- إنشاء سجل محاسبي من نوع `refund` في جدول `accounting_entries`
- يحتوي على:
  - `amount`: إجمالي مبلغ الاسترجاع
  - `commission_amount`: العمولة المعكوسة
  - `vendor_amount`: المبلغ الذي سيتم خصمه من رصيد التاجر
  - `metadata`: تفاصيل كاملة عن الاسترجاع

### 5. Payment Record ✅
- إذا كان الطلب مدفوع أونلاين، يتم إنشاء سجل `Payment` بحالة `refunded`
- يحتوي على رابط للدفعة الأصلية وتفاصيل الاسترجاع

### 6. Detailed Logging ✅
- تسجيل كامل لكل تفاصيل الاسترجاع في الـ Log

## Integration with Withdraw System

### How Refunds Affect Vendor Balance

في `Vendor` model، يتم حساب الرصيد المتاح للسحب كالتالي:

```php
// orders_price: إجمالي المعاملات (يستثني المنتجات المستردة)
// bnaia_commission: عمولة بنايا (يستثني المنتجات المستردة)
// total_balance = orders_price - bnaia_commission
// total_remaining = total_balance - total_sent_money
```

### Excluding Refunded Products

بدلاً من استخدام حقل `is_refunded` في جدول `order_products`، نستخدم العلاقة مع `refund_request_items`:

```sql
LEFT JOIN refund_request_items as rri ON op.id = rri.order_product_id
LEFT JOIN refund_requests as rr ON rri.refund_request_id = rr.id 
    AND rr.status = 'refunded'
WHERE rr.id IS NULL  -- Exclude products with completed refunds
```

**المميزات:**
- ✅ Single source of truth
- ✅ No data duplication
- ✅ Can track partial refunds
- ✅ Can check refund status and history

## Files Modified

### 1. RefundRequestObserver.php
- `handleRefundCompletion()`: معالجة اكتمال الاسترجاع
- `calculateCommissionReversal()`: حساب العمولة المعكوسة

### 2. Vendor.php
- `getBnaiaCommissionAttribute()`: استثناء المنتجات المستردة من حساب العمولة
- `getOrdersPriceAttribute()`: استثناء المنتجات المستردة من إجمالي المعاملات

## Commission Calculation

العمولة يتم حسابها على:
- السعر الإجمالي للمنتج (شامل الضريبة)
- تكلفة الشحن

```php
$refundableAmount = $item->total_price + $item->shipping_amount;
$commission = ($refundableAmount * $commissionPercent) / 100;
```

## Testing Checklist

- [ ] Test refund completion creates accounting entry
- [ ] Test vendor balance excludes refunded products
- [ ] Test commission calculation excludes refunded products
- [ ] Test withdraw system shows correct available balance
- [ ] Test partial refunds (some items from order)
- [ ] Test full refunds (all items from order)
- [ ] Test points deduction and return
- [ ] Test stock release

## Notes

- المنتجات المستردة **لا تظهر** في حسابات التاجر (orders_price, bnaia_commission)
- نظام السحب يعتمد على `total_balance` الذي يستثني المنتجات المستردة تلقائياً
- سجلات المحاسبة (AccountingEntry) توفر audit trail كامل للاسترجاعات
