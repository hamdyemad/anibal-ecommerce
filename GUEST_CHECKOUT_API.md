# Guest Checkout API Documentation

## Overview
تم إضافة إمكانية إنشاء طلبات للأشخاص غير المسجلين في النظام (Guest Checkout) من خلال نفس endpoint الـ checkout الموجود.

## Endpoint
```
POST /api/orders/checkout
```

## Authentication
- الـ endpoint يدعم الآن كلا من:
  - المستخدمين المسجلين (Authenticated Users)
  - الضيوف (Guest Users)

## Request Body

### Guest Checkout Request
```json
{
  "is_guest": true,
  "guest_first_name": "أحمد",
  "guest_last_name": "محمد",
  "guest_email": "ahmed@example.com",
  "guest_phone": "01234567890",
  "guest_country_id": 1,
  "guest_city_id": 5,
  "guest_region_id": 10,
  "guest_address": "شارع الجامعة، المعادي",
  "products": [
    {
      "vendor_product_id": 123,
      "vendor_product_variant_id": 456,
      "quantity": 2,
      "type": "product"
    },
    {
      "vendor_product_id": 789,
      "quantity": 1,
      "type": "product"
    }
  ],
  "promo_code_id": null,
  "order_from": "WEB",
  "payment_type": "cash_on_delivery"
}
```

### Existing Customer Checkout Request (لم يتغير)
```json
{
  "is_guest": false,
  "customer_address_id": 10,
  "promo_code_id": null,
  "order_from": "WEB",
  "payment_type": "cash_on_delivery",
  "use_point": false
}
```

## Request Parameters

### Guest Checkout Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `is_guest` | boolean | Yes | يجب أن يكون `true` للـ guest checkout |
| `guest_first_name` | string | Yes | الاسم الأول (max: 255) |
| `guest_last_name` | string | Yes | الاسم الأخير (max: 255) |
| `guest_email` | string | Yes | البريد الإلكتروني |
| `guest_phone` | string | Yes | رقم الهاتف (max: 20) |
| `guest_country_id` | integer | Yes | معرف الدولة |
| `guest_city_id` | integer | Yes | معرف المدينة (يستخدم لحساب الشحن) |
| `guest_region_id` | integer | No | معرف المنطقة |
| `guest_address` | string | No | العنوان التفصيلي (max: 500) |
| `products` | array | Yes | قائمة المنتجات (min: 1) |
| `products.*.vendor_product_id` | integer | Yes | معرف المنتج |
| `products.*.vendor_product_variant_id` | integer | No | معرف variant المنتج |
| `products.*.quantity` | integer | Yes | الكمية (min: 1) |
| `products.*.type` | string | No | نوع المنتج: product, bundle, occasion |
| `products.*.bundle_id` | integer | No | معرف الـ bundle |
| `products.*.occasion_id` | integer | No | معرف الـ occasion |
| `promo_code_id` | integer | No | معرف كود الخصم |
| `order_from` | string | No | مصدر الطلب: WEB, ANDROID, IOS |
| `payment_type` | string | No | طريقة الدفع: cash_on_delivery, online, aman |

## Shipping Calculation
- يتم حساب تكلفة الشحن تلقائياً بناءً على:
  - المدينة (`guest_city_id`)
  - فئة/قسم المنتجات
  - إعدادات الشحن المحددة في النظام

## Important Notes

### للـ Guest Users:
1. لا يمكن استخدام النقاط (Points) - `use_point` يتم تجاهله
2. يجب إرسال المنتجات مباشرة في الـ request (لا يوجد cart)
3. لا يتم حفظ الطلب مرتبط بـ customer_id (يكون null)
4. يتم حفظ بيانات العميل في جدول الطلبات مباشرة

### للـ Existing Customers:
1. يتم جلب المنتجات من الـ cart الخاص بهم
2. يمكن استخدام النقاط
3. يتم ربط الطلب بـ customer_id
4. يتم مسح الـ cart بعد إتمام الطلب

## Response
نفس الـ response للـ checkout العادي:

```json
{
  "message": "Order created successfully",
  "success": true,
  "data": {
    "id": 1234,
    "order_number": "ORD-240324-ABC123",
    "customer_name": "أحمد محمد",
    "customer_email": "ahmed@example.com",
    "customer_phone": "01234567890",
    "total_price": 500.00,
    "shipping": 50.00,
    "payment_type": "cash_on_delivery",
    "stage": {
      "id": 1,
      "name": "جديد",
      "type": "new"
    }
  }
}
```

## Error Responses

### Validation Errors
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "guest_email": ["The guest email field is required."],
    "guest_city_id": ["The guest city id field is required."],
    "products": ["The products field is required."]
  }
}
```

### Empty Products
```json
{
  "message": "Products are required for guest checkout",
  "success": false
}
```

## Example Usage

### cURL Example
```bash
curl -X POST https://your-domain.com/api/orders/checkout \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-Country-Code: EG" \
  -d '{
    "is_guest": true,
    "guest_first_name": "أحمد",
    "guest_last_name": "محمد",
    "guest_email": "ahmed@example.com",
    "guest_phone": "01234567890",
    "guest_country_id": 1,
    "guest_city_id": 5,
    "products": [
      {
        "vendor_product_id": 123,
        "quantity": 2
      }
    ],
    "payment_type": "cash_on_delivery",
    "order_from": "WEB"
  }'
```

### JavaScript Example
```javascript
const response = await fetch('https://your-domain.com/api/orders/checkout', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Country-Code': 'EG'
  },
  body: JSON.stringify({
    is_guest: true,
    guest_first_name: 'أحمد',
    guest_last_name: 'محمد',
    guest_email: 'ahmed@example.com',
    guest_phone: '01234567890',
    guest_country_id: 1,
    guest_city_id: 5,
    products: [
      {
        vendor_product_id: 123,
        quantity: 2
      }
    ],
    payment_type: 'cash_on_delivery',
    order_from: 'WEB'
  })
});

const data = await response.json();
console.log(data);
```

## Changes Made

### Modified Files:
1. `Modules/Order/app/Http/Requests/Api/CheckoutRequest.php`
   - إضافة validation للـ guest checkout
   - إضافة validation للـ products array

2. `Modules/Order/app/Services/Api/OrderApiService.php`
   - تعديل method `checkout()` للتعامل مع guest users

3. `Modules/Order/app/Pipelines/FetchUserData.php`
   - إضافة دعم `external_country_id`

4. `Modules/Order/app/Pipelines/FetchCartItems.php`
   - إضافة دعم المنتجات المرسلة مباشرة للـ guest users

5. `Modules/Order/app/Pipelines/EmptyCart.php`
   - تخطي مسح الـ cart للـ guest users

6. `Modules/Order/routes/api.php`
   - تغيير middleware من `auth:sanctum` إلى `auth.optional:sanctum`
