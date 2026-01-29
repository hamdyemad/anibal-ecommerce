# API Documentation
## Eramo Multi-Vendor E-Commerce Platform

**Version:** 1.0  
**Base URL:** `https://your-domain.com/api/v1`  
**Last Updated:** January 29, 2026

---

## Table of Contents

1. [Overview](#overview)
2. [Authentication](#authentication)
3. [Common Headers](#common-headers)
4. [Response Format](#response-format)
5. [Error Handling](#error-handling)
6. [Rate Limiting](#rate-limiting)
7. [API Endpoints](#api-endpoints)
   - [Authentication & Customer](#authentication--customer)
   - [Area Settings](#area-settings)
   - [Products & Catalog](#products--catalog)
   - [Cart & Wishlist](#cart--wishlist)
   - [Orders](#orders)
   - [Refunds](#refunds)
   - [Reviews](#reviews)
   - [Notifications](#notifications)
   - [Vendors](#vendors)
8. [Webhooks](#webhooks)
9. [Code Examples](#code-examples)

---

## Overview

The Eramo API is a RESTful API that allows you to interact with the multi-vendor e-commerce platform. All requests and responses are in JSON format.

### Key Features

- 🔐 Token-based authentication (Laravel Sanctum)
- 🌍 Multi-country support
- 🌐 Multi-language support (Arabic & English)
- 📦 Complex product variants
- 🛒 Shopping cart and wishlist
- 💳 Multiple payment methods
- 🔄 Refund management
- ⭐ Product reviews
- 🔔 Real-time notifications

---

## Authentication

The API uses **Laravel Sanctum** for authentication. After successful login/registration, you'll receive an access token.

### Authentication Flow

```
1. Register/Login → Receive access token
2. Include token in Authorization header for protected endpoints
3. Token expires after inactivity (configurable)
4. Refresh token or re-login when expired
```

### Token Usage

```http
Authorization: Bearer {your_access_token}
```

---

## Common Headers

All API requests should include these headers:

| Header | Required | Description | Example |
|--------|----------|-------------|---------|
| `Accept` | Yes | Response format | `application/json` |
| `Content-Type` | Yes (POST/PUT) | Request format | `application/json` |
| `Authorization` | Protected routes | Bearer token | `Bearer {token}` |
| `X-Country-Code` | Yes | Country code | `eg`, `sa`, `ae` |
| `lang` | Yes | Language preference | `en`, `ar` |

### Example Request

```http
GET /api/v1/products HTTP/1.1
Host: your-domain.com
Accept: application/json
X-Country-Code: eg
lang: en
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

---

## Response Format

All API responses follow a consistent structure:

### Success Response

```json
{
  "status": true,
  "message": "Success message",
  "errors": [],
  "data": {
    // Response data here
  }
}
```

### Error Response

```json
{
  "status": false,
  "message": "Error message",
  "errors": [
    "Detailed error 1",
    "Detailed error 2"
  ],
  "data": {}
}
```

### Paginated Response

```json
{
  "status": true,
  "message": "Success",
  "errors": [],
  "data": {
    "current_page": 1,
    "data": [...],
    "first_page_url": "...",
    "from": 1,
    "last_page": 10,
    "last_page_url": "...",
    "next_page_url": "...",
    "path": "...",
    "per_page": 15,
    "prev_page_url": null,
    "to": 15,
    "total": 150
  }
}
```

---

## Error Handling

### HTTP Status Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Request successful |
| 201 | Created | Resource created successfully |
| 400 | Bad Request | Invalid request parameters |
| 401 | Unauthorized | Authentication required or failed |
| 403 | Forbidden | Insufficient permissions |
| 404 | Not Found | Resource not found |
| 422 | Unprocessable Entity | Validation errors |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Server error |

### Validation Errors

```json
{
  "status": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  },
  "data": {}
}
```

---

## Rate Limiting

Different endpoints have different rate limits:

| Endpoint Type | Limit | Window |
|---------------|-------|--------|
| General API | 60 requests | 1 minute |
| Authentication | 10 requests | 1 minute |
| OTP Requests | 5 requests | 1 minute |
| Checkout | 10 requests | 1 minute |
| Product Listing | 120 requests | 1 minute |
| Vendor Request | 3 requests | 1 hour |

### Rate Limit Headers

```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1643723400
```

---

## API Endpoints

---

## Authentication & Customer

### 1. Customer Registration

**Endpoint:** `POST /auth/register`  
**Authentication:** Not required  
**Rate Limit:** 10/minute

Register a new customer account.

**Request Body:**

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+201234567890",
  "password": "password123",
  "password_confirmation": "password123",
  "country_id": 1,
  "city_id": 1,
  "region_id": 1,
  "address": "123 Main Street"
}
```

**Response:**

```json
{
  "status": true,
  "message": "Registration successful. Please verify your email/phone.",
  "errors": [],
  "data": {
    "customer": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+201234567890",
      "points_balance": 0,
      "is_active": true
    },
    "requires_verification": true,
    "verification_method": "otp"
  }
}
```

---

### 2. Verify OTP

**Endpoint:** `POST /auth/verify-otp`  
**Authentication:** Not required  
**Rate Limit:** 5/minute

Verify OTP sent to email/phone after registration.

**Request Body:**

```json
{
  "email": "john@example.com",
  "otp": "123456"
}
```

**Response:**

```json
{
  "status": true,
  "message": "Account verified successfully",
  "errors": [],
  "data": {
    "access_token": "1|eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer",
    "customer": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "points_balance": 0
    }
  }
}
```

---

### 3. Resend OTP

**Endpoint:** `POST /auth/resend-otp`  
**Authentication:** Not required  
**Rate Limit:** 5/minute

Resend OTP for verification.

**Request Body:**

```json
{
  "email": "john@example.com"
}
```

---

### 4. Customer Login

**Endpoint:** `POST /auth/login`  
**Authentication:** Not required  
**Rate Limit:** 10/minute

Login with email and password.

**Request Body:**

```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**

```json
{
  "status": true,
  "message": "Login successful",
  "errors": [],
  "data": {
    "access_token": "1|eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer",
    "customer": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+201234567890",
      "points_balance": 150,
      "country": {
        "id": 1,
        "name": "Egypt",
        "code": "eg"
      }
    }
  }
}
```

---

### 5. Get Profile

**Endpoint:** `GET /auth/profile`  
**Authentication:** Required

Get authenticated customer profile.

**Response:**

```json
{
  "status": true,
  "message": "Profile retrieved successfully",
  "errors": [],
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+201234567890",
    "points_balance": 150,
    "country": {...},
    "city": {...},
    "region": {...},
    "address": "123 Main Street",
    "created_at": "2026-01-15T10:30:00.000000Z"
  }
}
```

---

### 6. Update Profile

**Endpoint:** `POST /auth/update-profile`  
**Authentication:** Required

Update customer profile information.

**Request Body:**

```json
{
  "name": "John Updated",
  "phone": "+201234567890",
  "country_id": 1,
  "city_id": 1,
  "region_id": 1,
  "address": "456 New Street"
}
```

---

### 7. Logout

**Endpoint:** `POST /auth/logout`  
**Authentication:** Required

Logout and invalidate current token.

**Response:**

```json
{
  "status": true,
  "message": "Logged out successfully",
  "errors": [],
  "data": {}
}
```

---

### 8. Logout All Devices

**Endpoint:** `POST /auth/logout-devices`  
**Authentication:** Required

Logout from all devices (invalidate all tokens).

---

### 9. Request Password Reset

**Endpoint:** `POST /auth/request-password-reset`  
**Authentication:** Not required  
**Rate Limit:** 10/minute

Request password reset OTP.

**Request Body:**

```json
{
  "email": "john@example.com"
}
```

---

### 10. Verify Password Reset OTP

**Endpoint:** `POST /auth/verify-reset-otp`  
**Authentication:** Not required  
**Rate Limit:** 5/minute

Verify OTP for password reset.

**Request Body:**

```json
{
  "email": "john@example.com",
  "otp": "123456"
}
```

---

### 11. Reset Password

**Endpoint:** `POST /auth/reset-password`  
**Authentication:** Not required

Reset password after OTP verification.

**Request Body:**

```json
{
  "email": "john@example.com",
  "otp": "123456",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

---

### 12. Customer Addresses

**Endpoint:** `GET /addresses`  
**Authentication:** Required

Get all customer addresses.

**Response:**

```json
{
  "status": true,
  "message": "Addresses retrieved successfully",
  "errors": [],
  "data": [
    {
      "id": 1,
      "country": {...},
      "city": {...},
      "region": {...},
      "address_line_1": "123 Main Street",
      "address_line_2": "Apt 4B",
      "postal_code": "12345",
      "is_default": true
    }
  ]
}
```

---

### 13. Add Address

**Endpoint:** `POST /addresses`  
**Authentication:** Required

Add new customer address.

**Request Body:**

```json
{
  "country_id": 1,
  "city_id": 1,
  "region_id": 1,
  "address_line_1": "123 Main Street",
  "address_line_2": "Apt 4B",
  "postal_code": "12345",
  "is_default": true
}
```

---

### 14. Update Address

**Endpoint:** `POST /addresses/{addressId}`  
**Authentication:** Required

Update existing address.

---

### 15. Delete Address

**Endpoint:** `DELETE /addresses/{addressId}`  
**Authentication:** Required

Delete an address.

---

## Area Settings

### 1. Get Countries

**Endpoint:** `GET /area/countries`  
**Authentication:** Not required

Get all active countries.

**Response:**

```json
{
  "status": true,
  "message": "Countries retrieved successfully",
  "errors": [],
  "data": [
    {
      "id": 1,
      "name": "Egypt",
      "code": "eg",
      "currency": "EGP",
      "currency_symbol": "£",
      "phone_code": "+20",
      "is_active": true
    }
  ]
}
```

---

### 2. Get Cities by Country

**Endpoint:** `GET /area/countries/{countryId}/cities`  
**Authentication:** Not required

Get all cities for a specific country.

**Response:**

```json
{
  "status": true,
  "message": "Cities retrieved successfully",
  "errors": [],
  "data": [
    {
      "id": 1,
      "name": "Cairo",
      "country_id": 1,
      "is_active": true
    }
  ]
}
```

---

### 3. Get Regions by City

**Endpoint:** `GET /area/cities/{cityId}/regions`  
**Authentication:** Not required

Get all regions for a specific city.

**Response:**

```json
{
  "status": true,
  "message": "Regions retrieved successfully",
  "errors": [],
  "data": [
    {
      "id": 1,
      "name": "Nasr City",
      "city_id": 1,
      "shipping_cost": 50.00,
      "is_active": true
    }
  ]
}
```

---

## Products & Catalog

### 1. Get Products

**Endpoint:** `GET /products`  
**Authentication:** Optional  
**Rate Limit:** 120/minute

Get paginated list of products with filters.

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `per_page` | integer | Items per page (default: 15) |
| `page` | integer | Page number |
| `department_id` | integer | Filter by department |
| `main_category_id` | integer | Filter by main category |
| `category_id` | integer | Filter by category |
| `sub_category_id` | integer | Filter by sub-category |
| `brand_id` | integer | Filter by brand |
| `vendor_id` | integer | Filter by vendor |
| `min_price` | decimal | Minimum price |
| `max_price` | decimal | Maximum price |
| `search` | string | Search in product name |
| `sort_by` | string | Sort field (name, price, rating, views, sales) |
| `sort_type` | string | Sort direction (asc, desc) |

**Example Request:**

```http
GET /products?per_page=20&department_id=1&min_price=100&max_price=500&sort_by=price&sort_type=asc
```

**Response:**

```json
{
  "status": true,
  "message": "Products retrieved successfully",
  "errors": [],
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "vendor_id": 5,
        "slug": "product-name",
        "sku": "SKU123",
        "name": "Product Name",
        "details": "Product description",
        "image": "https://domain.com/storage/products/1/image.jpg",
        "images": ["..."],
        "price": 299.99,
        "points": 10,
        "reviews_count": 25,
        "review_avg_star": 4.5,
        "is_fav": false,
        "stock": 100,
        "remaining_stock": 95,
        "vendor": {
          "id": 5,
          "name": "Vendor Name",
          "logo": "..."
        },
        "brand": {
          "id": 3,
          "title": "Brand Name",
          "image": "..."
        },
        "department": {...},
        "category": {...},
        "variants": [...]
      }
    ],
    "per_page": 20,
    "total": 150,
    "last_page": 8
  }
}
```

---

### 2. Get Product Details

**Endpoint:** `GET /products/specific-product/{id}/{vendorId}`  
**Authentication:** Optional

Get detailed information about a specific product.

**Response:**

```json
{
  "status": true,
  "message": "Product retrieved successfully",
  "errors": [],
  "data": {
    "id": 1,
    "name": "Product Name",
    "details": "Full product description",
    "summary": "Short summary",
    "instructions": "Usage instructions",
    "features": "Product features",
    "extras": "Extra information",
    "material": "Material information",
    "video_link": "https://youtube.com/...",
    "images": [...],
    "variants": [
      {
        "id": 1,
        "price": 299.99,
        "sku": "VAR-001",
        "barcode": "123456789",
        "stock": 50,
        "configuration": {
          "color": "Red",
          "size": "Large"
        }
      }
    ],
    "taxes": [
      {
        "id": 1,
        "name": "VAT 15%",
        "percentage": 15
      }
    ],
    "reviews": [...],
    "related_products": [...]
  }
}
```

---

### 3. Get Product by Slug

**Endpoint:** `GET /products/product-by-slug/{slug}`  
**Authentication:** Optional

Get product by slug with all vendors selling it.

---

### 4. Get Featured Products

**Endpoint:** `GET /products/featured`  
**Authentication:** Optional

Get featured products.

---

### 5. Get Best Selling Products

**Endpoint:** `GET /products/best-selling`  
**Authentication:** Optional

Get best selling products.

---

### 6. Get Latest Products

**Endpoint:** `GET /products/latest`  
**Authentication:** Optional

Get latest added products.

---

### 7. Get Product Filters

**Endpoint:** `GET /products/filters`  
**Authentication:** Optional

Get available filters for products (brands, variants, price range).

**Query Parameters:** Same as product listing

**Response:**

```json
{
  "status": true,
  "message": "Filters retrieved successfully",
  "errors": [],
  "data": {
    "brands": [
      {
        "id": 1,
        "title": "Brand Name",
        "slug": "brand-name",
        "image": "...",
        "type": "brand"
      }
    ],
    "variants": [
      {
        "id": 1,
        "name": "Color",
        "options": [
          {"id": 1, "name": "Red", "color": "#FF0000"},
          {"id": 2, "name": "Blue", "color": "#0000FF"}
        ]
      }
    ],
    "price_range": {
      "min": 0,
      "max": 5000
    },
    "tags": ["tag1", "tag2"]
  }
}
```

---

### 8. Check Product Availability

**Endpoint:** `POST /products/check-availability`  
**Authentication:** Optional

Check if products/variants are available in stock.

**Request Body:**

```json
{
  "items": [
    {
      "vendor_product_id": 1,
      "vendor_product_variant_id": 5,
      "quantity": 2
    }
  ]
}
```

**Response:**

```json
{
  "status": true,
  "message": "Availability checked",
  "errors": [],
  "data": {
    "available": true,
    "items": [
      {
        "vendor_product_id": 1,
        "vendor_product_variant_id": 5,
        "requested_quantity": 2,
        "available_quantity": 50,
        "is_available": true
      }
    ]
  }
}
```

---

### 9. Get Brands

**Endpoint:** `GET /brands`  
**Authentication:** Not required

Get all active brands.

**Response:**

```json
{
  "status": true,
  "message": "Brands retrieved successfully",
  "errors": [],
  "data": [
    {
      "id": 1,
      "name": "Brand Name",
      "slug": "brand-name",
      "logo": "...",
      "cover": "...",
      "is_active": true
    }
  ]
}
```

---

### 10. Get Occasions

**Endpoint:** `GET /occasions`  
**Authentication:** Not required

Get all active occasions (special offers/sales).

**Response:**

```json
{
  "status": true,
  "message": "Occasions retrieved successfully",
  "errors": [],
  "data": [
    {
      "id": 1,
      "name": "Black Friday",
      "slug": "black-friday",
      "start_date": "2026-11-25",
      "end_date": "2026-11-30",
      "image": "...",
      "products_count": 150
    }
  ]
}
```

---

### 11. Get Bundles

**Endpoint:** `GET /bundles`  
**Authentication:** Not required

Get all active product bundles.

**Response:**

```json
{
  "status": true,
  "message": "Bundles retrieved successfully",
  "errors": [],
  "data": [
    {
      "id": 1,
      "name": "Bundle Name",
      "slug": "bundle-name",
      "discount_type": "percentage",
      "discount_value": 20,
      "start_date": "2026-01-01",
      "end_date": "2026-12-31",
      "image": "...",
      "products": [...]
    }
  ]
}
```

---

## Cart & Wishlist

### Cart Endpoints

#### 1. Get Cart

**Endpoint:** `GET /cart`  
**Authentication:** Required

Get customer's cart items.

**Response:**

```json
{
  "status": true,
  "message": "Cart retrieved successfully",
  "errors": [],
  "data": {
    "items": [
      {
        "id": 1,
        "vendor_product_id": 5,
        "vendor_product_variant_id": 10,
        "quantity": 2,
        "product": {
          "id": 5,
          "name": "Product Name",
          "image": "...",
          "price": 299.99,
          "vendor": {...}
        },
        "variant": {
          "id": 10,
          "price": 299.99,
          "configuration": {
            "color": "Red",
            "size": "Large"
          }
        },
        "subtotal": 599.98
      }
    ],
    "summary": {
      "items_count": 2,
      "subtotal": 599.98,
      "tax": 89.99,
      "shipping": 50.00,
      "total": 739.97
    }
  }
}
```

---

#### 2. Add to Cart

**Endpoint:** `POST /cart/add-or-update`  
**Authentication:** Required

Add item to cart or update quantity if exists.

**Request Body:**

```json
{
  "vendor_product_id": 5,
  "vendor_product_variant_id": 10,
  "quantity": 2
}
```

**Response:**

```json
{
  "status": true,
  "message": "Item added to cart successfully",
  "errors": [],
  "data": {
    "cart_item": {...},
    "cart_count": 3
  }
}
```

---

#### 3. Add Bulk to Cart

**Endpoint:** `POST /cart/add-bulk`  
**Authentication:** Required

Add multiple items to cart at once.

**Request Body:**

```json
{
  "items": [
    {
      "vendor_product_id": 5,
      "vendor_product_variant_id": 10,
      "quantity": 2
    },
    {
      "vendor_product_id": 6,
      "vendor_product_variant_id": 12,
      "quantity": 1
    }
  ]
}
```

---

#### 4. Remove from Cart

**Endpoint:** `DELETE /cart/remove/{cartItemId}`  
**Authentication:** Required

Remove item from cart.

---

#### 5. Clear Cart

**Endpoint:** `POST /cart/clear`  
**Authentication:** Required

Remove all items from cart.

---

#### 6. Get Cart Count

**Endpoint:** `GET /cart/count`  
**Authentication:** Required

Get total number of items in cart.

**Response:**

```json
{
  "status": true,
  "message": "Cart count retrieved",
  "errors": [],
  "data": {
    "count": 5
  }
}
```

---

#### 7. Get Cart Summary

**Endpoint:** `GET /cart/summary`  
**Authentication:** Required

Get cart summary with totals.

---

### Wishlist Endpoints

#### 1. Get Wishlist

**Endpoint:** `GET /wishlist`  
**Authentication:** Required

Get customer's wishlist items.

**Response:**

```json
{
  "status": true,
  "message": "Wishlist retrieved successfully",
  "errors": [],
  "data": [
    {
      "id": 1,
      "vendor_product_id": 5,
      "product": {
        "id": 5,
        "name": "Product Name",
        "image": "...",
        "price": 299.99,
        "vendor": {...}
      },
      "added_at": "2026-01-15T10:30:00.000000Z"
    }
  ]
}
```

---

#### 2. Add to Wishlist

**Endpoint:** `POST /wishlist/add`  
**Authentication:** Required

Add product to wishlist.

**Request Body:**

```json
{
  "vendor_product_id": 5
}
```

---

#### 3. Remove from Wishlist

**Endpoint:** `POST /wishlist/remove`  
**Authentication:** Required

Remove product from wishlist.

**Request Body:**

```json
{
  "vendor_product_id": 5
}
```

---

#### 4. Clear Wishlist

**Endpoint:** `POST /wishlist/clear`  
**Authentication:** Required

Remove all items from wishlist.

---

#### 5. Check if in Wishlist

**Endpoint:** `GET /wishlist/check?vendor_product_id=5`  
**Authentication:** Required

Check if product is in wishlist.

**Response:**

```json
{
  "status": true,
  "message": "Check completed",
  "errors": [],
  "data": {
    "in_wishlist": true
  }
}
```

---

#### 6. Get Wishlist Count

**Endpoint:** `GET /wishlist/count`  
**Authentication:** Required

Get total number of items in wishlist.

---

## Orders

### 1. Checkout

**Endpoint:** `POST /orders/checkout`  
**Authentication:** Required  
**Rate Limit:** 10/minute

Create a new order from cart.

**Request Body:**

```json
{
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "customer_phone": "+201234567890",
  "customer_address": "123 Main Street",
  "country_id": 1,
  "city_id": 1,
  "region_id": 1,
  "payment_type": "cash",
  "promo_code": "SAVE20",
  "points_to_use": 50,
  "notes": "Please deliver in the morning"
}
```

**Payment Types:**
- `cash` - Cash on delivery
- `visa` - Credit/debit card
- `points` - Pay with loyalty points

**Response:**

```json
{
  "status": true,
  "message": "Order created successfully",
  "errors": [],
  "data": {
    "order": {
      "id": 123,
      "order_number": "ORD-2026-00123",
      "customer_id": 1,
      "total_price": 739.97,
      "payment_type": "cash",
      "stage": "new",
      "items_count": 3,
      "created_at": "2026-01-29T10:30:00.000000Z"
    },
    "payment_url": null
  }
}
```

---

### 2. Get My Orders

**Endpoint:** `GET /orders`  
**Authentication:** Required

Get customer's orders with pagination.

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `per_page` | integer | Items per page |
| `page` | integer | Page number |
| `stage_id` | integer | Filter by order stage |
| `date_from` | date | Filter from date (YYYY-MM-DD) |
| `date_to` | date | Filter to date (YYYY-MM-DD) |

**Response:**

```json
{
  "status": true,
  "message": "Orders retrieved successfully",
  "errors": [],
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 123,
        "order_number": "ORD-2026-00123",
        "total_price": 739.97,
        "payment_type": "cash",
        "stage": {
          "id": 1,
          "name": "New",
          "type": "new"
        },
        "items_count": 3,
        "created_at": "2026-01-29T10:30:00.000000Z"
      }
    ],
    "per_page": 15,
    "total": 50
  }
}
```

---

### 3. Get Order Details

**Endpoint:** `GET /orders/{orderId}`  
**Authentication:** Required

Get detailed information about a specific order.

**Response:**

```json
{
  "status": true,
  "message": "Order retrieved successfully",
  "errors": [],
  "data": {
    "id": 123,
    "order_number": "ORD-2026-00123",
    "customer_name": "John Doe",
    "customer_email": "john@example.com",
    "customer_phone": "+201234567890",
    "customer_address": "123 Main Street",
    "payment_type": "cash",
    "total_product_price": 599.98,
    "shipping": 50.00,
    "total_tax": 89.99,
    "total_price": 739.97,
    "points_used": 50,
    "points_cost": 25.00,
    "stage": {...},
    "products": [
      {
        "id": 1,
        "product_name": "Product Name",
        "variant_name": "Red - Large",
        "quantity": 2,
        "price": 299.99,
        "total_price": 599.98,
        "image": "...",
        "stage": {...}
      }
    ],
    "vendor_stages": [
      {
        "vendor": {...},
        "stage": {...},
        "total_amount": 599.98,
        "commission_amount": 59.99
      }
    ],
    "payments": [...],
    "created_at": "2026-01-29T10:30:00.000000Z"
  }
}
```

---

### 4. Cancel Order

**Endpoint:** `POST /orders/{orderId}/cancel`  
**Authentication:** Required

Cancel an order (only if in 'new' stage).

**Response:**

```json
{
  "status": true,
  "message": "Order cancelled successfully",
  "errors": [],
  "data": {
    "order": {...}
  }
}
```

---

### 5. Return Order

**Endpoint:** `POST /orders/{orderId}/return`  
**Authentication:** Required

Request return for delivered order.

---

### 6. Check Promo Code

**Endpoint:** `POST /promocode/check`  
**Authentication:** Not required

Validate a promo code.

**Request Body:**

```json
{
  "code": "SAVE20"
}
```

**Response:**

```json
{
  "status": true,
  "message": "Promo code is valid",
  "errors": [],
  "data": {
    "code": "SAVE20",
    "type": "percentage",
    "value": 20,
    "maximum_of_use": 100,
    "current_usage": 45,
    "start_date": "2026-01-01",
    "end_date": "2026-12-31"
  }
}
```

---

### 7. Calculate Shipping

**Endpoint:** `POST /shipping/calculate`  
**Authentication:** Required

Calculate shipping cost for cart.

**Request Body:**

```json
{
  "region_id": 1
}
```

**Response:**

```json
{
  "status": true,
  "message": "Shipping calculated",
  "errors": [],
  "data": {
    "shipping_cost": 50.00,
    "region": {...}
  }
}
```

---

### 8. Get Order Stages

**Endpoint:** `GET /order-stages`  
**Authentication:** Not required

Get all available order stages.

**Response:**

```json
{
  "status": true,
  "message": "Order stages retrieved",
  "errors": [],
  "data": [
    {
      "id": 1,
      "name": "New",
      "type": "new",
      "sort_number": 1
    },
    {
      "id": 2,
      "name": "Processing",
      "type": "processing",
      "sort_number": 2
    }
  ]
}
```

---

## Refunds

### 1. Get Refund Requests

**Endpoint:** `GET /refunds`  
**Authentication:** Required

Get customer's refund requests.

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `per_page` | integer | Items per page |
| `status` | string | Filter by status |
| `vendor_id` | integer | Filter by vendor |
| `date_from` | date | Filter from date |
| `date_to` | date | Filter to date |

**Response:**

```json
{
  "status": true,
  "message": "Refund requests retrieved successfully",
  "errors": [],
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "refund_number": "REF-2026-00001",
        "order_number": "ORD-2026-00123",
        "status": "pending",
        "status_label": "Pending",
        "reason": "Product damaged",
        "total_refund_amount": 299.99,
        "vendor": {...},
        "created_at": "2026-01-29T10:30:00.000000Z"
      }
    ],
    "per_page": 15,
    "total": 10
  }
}
```

---

### 2. Get Refund Details

**Endpoint:** `GET /refunds/{id}`  
**Authentication:** Required

Get detailed information about a refund request.

**Response:**

```json
{
  "status": true,
  "message": "Refund request retrieved successfully",
  "errors": [],
  "data": {
    "id": 1,
    "refund_number": "REF-2026-00001",
    "order": {...},
    "vendor": {...},
    "status": "pending",
    "status_label": "Pending",
    "reason": "Product damaged",
    "customer_notes": "The product arrived damaged",
    "vendor_notes": null,
    "admin_notes": null,
    "items": [
      {
        "id": 1,
        "order_product": {...},
        "quantity": 1,
        "unit_price_without_tax": 260.86,
        "unit_tax_amount": 39.13,
        "total_amount": 299.99
      }
    ],
    "total_products_amount": 260.86,
    "total_tax_amount": 39.13,
    "total_shipping_amount": 0.00,
    "return_shipping_cost": 25.00,
    "total_refund_amount": 274.86,
    "customer_pays_return_shipping": true,
    "history": [
      {
        "id": 1,
        "old_status": null,
        "new_status": "pending",
        "changed_by": "Customer",
        "notes": "Refund request created",
        "created_at": "2026-01-29T10:30:00.000000Z"
      }
    ],
    "created_at": "2026-01-29T10:30:00.000000Z"
  }
}
```

---

### 3. Create Refund Request

**Endpoint:** `POST /refunds`  
**Authentication:** Required

Create a new refund request for an order.

**Request Body:**

```json
{
  "order_id": 123,
  "reason": "Product damaged",
  "customer_notes": "The product arrived damaged and unusable",
  "items": [
    {
      "order_product_id": 1,
      "quantity": 1
    },
    {
      "order_product_id": 2,
      "quantity": 2
    }
  ],
  "customer_pays_return_shipping": true,
  "return_shipping_cost": 25.00
}
```

**Response:**

```json
{
  "status": true,
  "message": "Refund request created successfully",
  "errors": [],
  "data": {
    "refund_requests": [
      {
        "id": 1,
        "refund_number": "REF-2026-00001",
        "vendor_id": 5,
        "status": "pending"
      }
    ]
  }
}
```

**Note:** Multiple refund requests may be created if order contains products from multiple vendors.

---

### 4. Cancel Refund Request

**Endpoint:** `POST /refunds/{id}/cancel`  
**Authentication:** Required

Cancel a pending refund request.

**Request Body:**

```json
{
  "cancellation_reason": "Changed my mind"
}
```

**Response:**

```json
{
  "status": true,
  "message": "Refund request cancelled successfully",
  "errors": [],
  "data": {}
}
```

**Note:** Only pending refunds can be cancelled by customers.

---

### 5. Get Refund Statistics

**Endpoint:** `GET /refunds/statistics`  
**Authentication:** Required

Get refund statistics for customer.

**Response:**

```json
{
  "status": true,
  "message": "Statistics retrieved successfully",
  "errors": [],
  "data": {
    "total": 10,
    "pending": 2,
    "approved": 1,
    "in_progress": 1,
    "picked_up": 0,
    "refunded": 5,
    "cancelled": 1,
    "total_amount": 2499.50
  }
}
```

---

### 6. Get Refund Statuses

**Endpoint:** `GET /refunds/statuses`  
**Authentication:** Required

Get all available refund statuses with translations.

**Response:**

```json
{
  "status": true,
  "message": "Statuses retrieved successfully",
  "errors": [],
  "data": [
    {
      "id": "pending",
      "value": "pending",
      "label": "Pending",
      "label_en": "Pending",
      "label_ar": "قيد الانتظار"
    },
    {
      "id": "approved",
      "value": "approved",
      "label": "Approved",
      "label_en": "Approved",
      "label_ar": "موافق عليه"
    }
  ]
}
```

---

## Reviews

### 1. Get Product Reviews

**Endpoint:** `GET /vendor-product/{reviewableId}/reviews`  
**Authentication:** Optional

Get reviews for a specific product.

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `per_page` | integer | Items per page |
| `page` | integer | Page number |

**Response:**

```json
{
  "status": true,
  "message": "Reviews retrieved successfully",
  "errors": [],
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "customer": {
          "id": 1,
          "name": "John Doe"
        },
        "star": 5,
        "comment": "Excellent product!",
        "is_approved": true,
        "created_at": "2026-01-29T10:30:00.000000Z"
      }
    ],
    "per_page": 15,
    "total": 25,
    "average_rating": 4.5
  }
}
```

---

### 2. Create Review

**Endpoint:** `POST /vendor-product/{reviewableId}/reviews`  
**Authentication:** Required

Create a review for a product.

**Request Body:**

```json
{
  "star": 5,
  "comment": "Excellent product! Highly recommended."
}
```

**Response:**

```json
{
  "status": true,
  "message": "Review submitted successfully. It will be visible after approval.",
  "errors": [],
  "data": {
    "review": {
      "id": 1,
      "star": 5,
      "comment": "Excellent product! Highly recommended.",
      "is_approved": false,
      "created_at": "2026-01-29T10:30:00.000000Z"
    }
  }
}
```

---

### 3. Get My Reviews

**Endpoint:** `GET /reviews/my-reviews`  
**Authentication:** Required

Get all reviews created by authenticated customer.

**Response:**

```json
{
  "status": true,
  "message": "Reviews retrieved successfully",
  "errors": [],
  "data": [
    {
      "id": 1,
      "product": {...},
      "star": 5,
      "comment": "Excellent product!",
      "is_approved": true,
      "created_at": "2026-01-29T10:30:00.000000Z"
    }
  ]
}
```

---

## Customer Points

### 1. Get My Points

**Endpoint:** `GET /points/my-points`  
**Authentication:** Required

Get customer's points balance and information.

**Response:**

```json
{
  "status": true,
  "message": "Points retrieved successfully",
  "errors": [],
  "data": {
    "points_balance": 150,
    "points_value": 75.00,
    "points_per_currency": 2,
    "currency": "EGP",
    "currency_symbol": "£"
  }
}
```

---

### 2. Get Points Transactions

**Endpoint:** `GET /points/transactions`  
**Authentication:** Required

Get customer's points transaction history.

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `per_page` | integer | Items per page |
| `page` | integer | Page number |
| `type` | string | Filter by type (credit, debit) |

**Response:**

```json
{
  "status": true,
  "message": "Transactions retrieved successfully",
  "errors": [],
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "points": "50",
        "type": "credit",
        "type_label": "Credit",
        "description": "Points earned from order #ORD-2026-00123",
        "balance_after": 150,
        "created_at": "2026-01-29T10:30:00.000000Z"
      },
      {
        "id": 2,
        "points": "25",
        "type": "debit",
        "type_label": "Debit",
        "description": "Points used in order #ORD-2026-00124",
        "balance_after": 125,
        "created_at": "2026-01-28T15:20:00.000000Z"
      }
    ],
    "per_page": 15,
    "total": 50
  }
}
```

---

### 3. Get Points Settings

**Endpoint:** `GET /points/settings`  
**Authentication:** Required

Get points system settings.

**Response:**

```json
{
  "status": true,
  "message": "Settings retrieved successfully",
  "errors": [],
  "data": {
    "points_per_currency": 2,
    "currency": "EGP",
    "currency_symbol": "£",
    "earn_rate": 1,
    "minimum_order_for_points": 100,
    "points_expiry_days": 365
  }
}
```

---

## Notifications

### 1. Get Notifications

**Endpoint:** `GET /notifications`  
**Authentication:** Required

Get customer's notifications.

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `per_page` | integer | Items per page |
| `page` | integer | Page number |
| `read` | boolean | Filter by read status |

**Response:**

```json
{
  "status": true,
  "message": "Notifications retrieved successfully",
  "errors": [],
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": "uuid-string",
        "type": "order",
        "title": "Order Confirmed",
        "message": "Your order #ORD-2026-00123 has been confirmed",
        "data": {
          "order_id": 123,
          "order_number": "ORD-2026-00123"
        },
        "read_at": null,
        "created_at": "2026-01-29T10:30:00.000000Z"
      }
    ],
    "per_page": 15,
    "total": 50
  }
}
```

---

### 2. Get Notification Details

**Endpoint:** `GET /notifications/{id}`  
**Authentication:** Required

Get specific notification details.

---

### 3. Mark Notification as Read

**Endpoint:** `POST /notifications/{id}/read`  
**Authentication:** Required

Mark a notification as read.

**Response:**

```json
{
  "status": true,
  "message": "Notification marked as read",
  "errors": [],
  "data": {}
}
```

---

### 4. Mark All as Read

**Endpoint:** `POST /notifications/read-all`  
**Authentication:** Required

Mark all notifications as read.

---

### 5. Get Unread Count

**Endpoint:** `GET /notifications/unread-count`  
**Authentication:** Required

Get count of unread notifications.

**Response:**

```json
{
  "status": true,
  "message": "Unread count retrieved",
  "errors": [],
  "data": {
    "unread_count": 5
  }
}
```

---

## Vendors

### 1. Get Vendors

**Endpoint:** `GET /vendors`  
**Authentication:** Not required

Get all active vendors.

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `per_page` | integer | Items per page |
| `search` | string | Search vendor name |

**Response:**

```json
{
  "status": true,
  "message": "Vendors retrieved successfully",
  "errors": [],
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Vendor Name",
        "slug": "vendor-name",
        "email": "vendor@example.com",
        "phone": "+201234567890",
        "logo": "...",
        "cover": "...",
        "is_active": true,
        "products_count": 150,
        "average_rating": 4.5
      }
    ],
    "per_page": 15,
    "total": 50
  }
}
```

---

### 2. Get Vendor Details

**Endpoint:** `GET /vendors/{id}`  
**Authentication:** Not required

Get detailed information about a vendor.

**Response:**

```json
{
  "status": true,
  "message": "Vendor retrieved successfully",
  "errors": [],
  "data": {
    "id": 1,
    "name": "Vendor Name",
    "slug": "vendor-name",
    "email": "vendor@example.com",
    "phone": "+201234567890",
    "address": "123 Vendor Street",
    "logo": "...",
    "cover": "...",
    "description": "Vendor description",
    "is_active": true,
    "products_count": 150,
    "average_rating": 4.5,
    "total_reviews": 200,
    "country": {...},
    "city": {...},
    "departments": [...]
  }
}
```

---

### 3. Vendor Request

**Endpoint:** `POST /vendor-request`  
**Authentication:** Not required  
**Rate Limit:** 3/hour

Submit a request to become a vendor.

**Request Body:**

```json
{
  "name": "My Business",
  "email": "business@example.com",
  "phone": "+201234567890",
  "country_id": 1,
  "city_id": 1,
  "commercial_register": "CR123456",
  "tax_number": "TAX789",
  "description": "Business description",
  "department_ids": [1, 2, 3]
}
```

**Response:**

```json
{
  "status": true,
  "message": "Vendor request submitted successfully. We will review and contact you soon.",
  "errors": [],
  "data": {
    "request_id": 1
  }
}
```

---

## Request Quotations

### 1. Get Quotations

**Endpoint:** `GET /request-quotations`  
**Authentication:** Required

Get customer's quotation requests.

**Response:**

```json
{
  "status": true,
  "message": "Quotations retrieved successfully",
  "errors": [],
  "data": [
    {
      "id": 1,
      "product_name": "Custom Product",
      "quantity": 100,
      "description": "Need bulk order",
      "status": "pending",
      "vendor_offer": null,
      "created_at": "2026-01-29T10:30:00.000000Z"
    }
  ]
}
```

---

### 2. Create Quotation Request

**Endpoint:** `POST /request-quotations`  
**Authentication:** Required

Request a quotation for custom/bulk order.

**Request Body:**

```json
{
  "product_name": "Custom Product",
  "quantity": 100,
  "description": "Need bulk order with custom specifications",
  "vendor_id": 5
}
```

---

### 3. Respond to Quotation Offer

**Endpoint:** `POST /request-quotations/{id}/respond`  
**Authentication:** Required

Accept or reject vendor's quotation offer.

**Request Body:**

```json
{
  "response": "accept"
}
```

---

## Payment Integration (Paymob)

### 1. Create Payment

**Endpoint:** `POST /paymob/create`  
**Authentication:** Not required

Create a payment intent with Paymob.

**Request Body:**

```json
{
  "order_id": 123,
  "amount": 739.97
}
```

**Response:**

```json
{
  "status": true,
  "message": "Payment created successfully",
  "errors": [],
  "data": {
    "payment_url": "https://accept.paymob.com/...",
    "payment_token": "token_string"
  }
}
```

---

### 2. Payment Callback

**Endpoint:** `GET|POST /paymob/callback`  
**Authentication:** Not required

Paymob redirects customer here after payment.

---

### 3. Payment Webhook

**Endpoint:** `POST /paymob/webhook`  
**Authentication:** Not required

Paymob sends payment status updates here.

---

### 4. Check Payment Status

**Endpoint:** `GET /paymob/check/{paymob_order_id}`  
**Authentication:** Not required

Check payment status.

**Response:**

```json
{
  "status": true,
  "message": "Payment status retrieved",
  "errors": [],
  "data": {
    "order_id": 123,
    "payment_status": "completed",
    "amount": 739.97,
    "transaction_id": "txn_123456"
  }
}
```

---

## Webhooks

The API sends webhooks for important events. Configure webhook URLs in admin panel.

### Webhook Events

| Event | Description |
|-------|-------------|
| `order.created` | New order created |
| `order.updated` | Order status changed |
| `order.cancelled` | Order cancelled |
| `refund.created` | Refund request created |
| `refund.updated` | Refund status changed |
| `payment.completed` | Payment successful |
| `payment.failed` | Payment failed |

### Webhook Payload

```json
{
  "event": "order.created",
  "timestamp": "2026-01-29T10:30:00.000000Z",
  "data": {
    "order_id": 123,
    "order_number": "ORD-2026-00123",
    "customer_id": 1,
    "total_price": 739.97
  }
}
```

### Webhook Security

All webhooks include a signature header for verification:

```http
X-Webhook-Signature: sha256_hash_of_payload
```

---

## Code Examples

### JavaScript (Fetch API)

#### Authentication

```javascript
// Register
const register = async () => {
  const response = await fetch('https://your-domain.com/api/v1/auth/register', {
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-Country-Code': 'eg',
      'lang': 'en'
    },
    body: JSON.stringify({
      name: 'John Doe',
      email: 'john@example.com',
      phone: '+201234567890',
      password: 'password123',
      password_confirmation: 'password123',
      country_id: 1,
      city_id: 1,
      region_id: 1,
      address: '123 Main Street'
    })
  });
  
  const data = await response.json();
  console.log(data);
};

// Login
const login = async () => {
  const response = await fetch('https://your-domain.com/api/v1/auth/login', {
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-Country-Code': 'eg',
      'lang': 'en'
    },
    body: JSON.stringify({
      email: 'john@example.com',
      password: 'password123'
    })
  });
  
  const data = await response.json();
  
  if (data.status) {
    // Store token
    localStorage.setItem('access_token', data.data.access_token);
  }
  
  return data;
};
```

#### Get Products

```javascript
const getProducts = async (filters = {}) => {
  const token = localStorage.getItem('access_token');
  
  // Build query string
  const params = new URLSearchParams(filters);
  
  const response = await fetch(`https://your-domain.com/api/v1/products?${params}`, {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-Country-Code': 'eg',
      'lang': 'en',
      'Authorization': `Bearer ${token}`
    }
  });
  
  const data = await response.json();
  return data;
};

// Usage
getProducts({
  per_page: 20,
  department_id: 1,
  min_price: 100,
  max_price: 500,
  sort_by: 'price',
  sort_type: 'asc'
});
```

#### Add to Cart

```javascript
const addToCart = async (vendorProductId, variantId, quantity) => {
  const token = localStorage.getItem('access_token');
  
  const response = await fetch('https://your-domain.com/api/v1/cart/add-or-update', {
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-Country-Code': 'eg',
      'lang': 'en',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify({
      vendor_product_id: vendorProductId,
      vendor_product_variant_id: variantId,
      quantity: quantity
    })
  });
  
  const data = await response.json();
  return data;
};
```

#### Checkout

```javascript
const checkout = async (orderData) => {
  const token = localStorage.getItem('access_token');
  
  const response = await fetch('https://your-domain.com/api/v1/orders/checkout', {
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-Country-Code': 'eg',
      'lang': 'en',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify({
      customer_name: orderData.name,
      customer_email: orderData.email,
      customer_phone: orderData.phone,
      customer_address: orderData.address,
      country_id: orderData.country_id,
      city_id: orderData.city_id,
      region_id: orderData.region_id,
      payment_type: orderData.payment_type,
      promo_code: orderData.promo_code,
      points_to_use: orderData.points_to_use
    })
  });
  
  const data = await response.json();
  return data;
};
```

---

### PHP (cURL)

```php
<?php

class EramoAPI {
    private $baseUrl = 'https://your-domain.com/api/v1';
    private $token = null;
    private $countryCode = 'eg';
    private $lang = 'en';
    
    public function __construct($token = null) {
        $this->token = $token;
    }
    
    private function request($method, $endpoint, $data = null) {
        $ch = curl_init($this->baseUrl . $endpoint);
        
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'X-Country-Code: ' . $this->countryCode,
            'lang: ' . $this->lang
        ];
        
        if ($this->token) {
            $headers[] = 'Authorization: Bearer ' . $this->token;
        }
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return json_decode($response, true);
    }
    
    public function login($email, $password) {
        $response = $this->request('POST', '/auth/login', [
            'email' => $email,
            'password' => $password
        ]);
        
        if ($response['status']) {
            $this->token = $response['data']['access_token'];
        }
        
        return $response;
    }
    
    public function getProducts($filters = []) {
        $query = http_build_query($filters);
        return $this->request('GET', '/products?' . $query);
    }
    
    public function addToCart($vendorProductId, $variantId, $quantity) {
        return $this->request('POST', '/cart/add-or-update', [
            'vendor_product_id' => $vendorProductId,
            'vendor_product_variant_id' => $variantId,
            'quantity' => $quantity
        ]);
    }
    
    public function checkout($orderData) {
        return $this->request('POST', '/orders/checkout', $orderData);
    }
}

// Usage
$api = new EramoAPI();

// Login
$loginResponse = $api->login('john@example.com', 'password123');

// Get products
$products = $api->getProducts([
    'per_page' => 20,
    'department_id' => 1,
    'min_price' => 100,
    'max_price' => 500
]);

// Add to cart
$cartResponse = $api->addToCart(5, 10, 2);

// Checkout
$orderResponse = $api->checkout([
    'customer_name' => 'John Doe',
    'customer_email' => 'john@example.com',
    'customer_phone' => '+201234567890',
    'customer_address' => '123 Main Street',
    'country_id' => 1,
    'city_id' => 1,
    'region_id' => 1,
    'payment_type' => 'cash'
]);
```

---

### Python (Requests)

```python
import requests

class EramoAPI:
    def __init__(self, base_url='https://your-domain.com/api/v1', token=None):
        self.base_url = base_url
        self.token = token
        self.country_code = 'eg'
        self.lang = 'en'
    
    def _headers(self):
        headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Country-Code': self.country_code,
            'lang': self.lang
        }
        
        if self.token:
            headers['Authorization'] = f'Bearer {self.token}'
        
        return headers
    
    def login(self, email, password):
        response = requests.post(
            f'{self.base_url}/auth/login',
            headers=self._headers(),
            json={
                'email': email,
                'password': password
            }
        )
        
        data = response.json()
        
        if data['status']:
            self.token = data['data']['access_token']
        
        return data
    
    def get_products(self, filters=None):
        response = requests.get(
            f'{self.base_url}/products',
            headers=self._headers(),
            params=filters
        )
        
        return response.json()
    
    def add_to_cart(self, vendor_product_id, variant_id, quantity):
        response = requests.post(
            f'{self.base_url}/cart/add-or-update',
            headers=self._headers(),
            json={
                'vendor_product_id': vendor_product_id,
                'vendor_product_variant_id': variant_id,
                'quantity': quantity
            }
        )
        
        return response.json()
    
    def checkout(self, order_data):
        response = requests.post(
            f'{self.base_url}/orders/checkout',
            headers=self._headers(),
            json=order_data
        )
        
        return response.json()

# Usage
api = EramoAPI()

# Login
login_response = api.login('john@example.com', 'password123')

# Get products
products = api.get_products({
    'per_page': 20,
    'department_id': 1,
    'min_price': 100,
    'max_price': 500
})

# Add to cart
cart_response = api.add_to_cart(5, 10, 2)

# Checkout
order_response = api.checkout({
    'customer_name': 'John Doe',
    'customer_email': 'john@example.com',
    'customer_phone': '+201234567890',
    'customer_address': '123 Main Street',
    'country_id': 1,
    'city_id': 1,
    'region_id': 1,
    'payment_type': 'cash'
})
```

---

## Testing

### Postman Collection

Import the Postman collection for easy API testing:

**Collection URL:** `https://your-domain.com/api-docs/postman-collection.json`

### Test Credentials

**Test Customer Account:**
- Email: `test@example.com`
- Password: `password123`

**Test Country Codes:**
- Egypt: `eg`
- Saudi Arabia: `sa`
- UAE: `ae`

---

## Support & Resources

### Documentation
- **API Docs:** `https://your-domain.com/api-docs`
- **OpenAPI Spec:** `https://your-domain.com/api-docs/openapi.json`
- **Postman Collection:** `https://your-domain.com/api-docs/postman-collection.json`

### Support
- **Email:** support@your-domain.com
- **Developer Portal:** `https://developers.your-domain.com`
- **Status Page:** `https://status.your-domain.com`

### Changelog
- **v1.0.0** (2026-01-29) - Initial API release

---

## Best Practices

### 1. Error Handling

Always check the `status` field in responses:

```javascript
const response = await fetch(url, options);
const data = await response.json();

if (!data.status) {
  // Handle error
  console.error(data.message);
  console.error(data.errors);
} else {
  // Success
  console.log(data.data);
}
```

### 2. Token Management

- Store tokens securely (not in localStorage for sensitive apps)
- Implement token refresh logic
- Handle 401 responses by redirecting to login

### 3. Rate Limiting

- Implement exponential backoff for rate limit errors
- Cache responses when possible
- Use pagination efficiently

### 4. Performance

- Use appropriate `per_page` values
- Implement lazy loading for lists
- Cache static data (countries, categories)
- Use CDN for images

### 5. Security

- Always use HTTPS
- Validate user input
- Don't expose tokens in URLs
- Implement CSRF protection for web apps

---

**Document Version:** 1.0  
**Last Updated:** January 29, 2026  
**API Version:** v1  
**Base URL:** `https://your-domain.com/api/v1`

---

**Related Documentation:**
- [Project Architecture](./PROJECT_ARCHITECTURE_AND_STRATEGY.md)
- [Database Design](./DATABASE_DESIGN.md)
- [Admin Panel Documentation](./ADMIN_PANEL.md)
