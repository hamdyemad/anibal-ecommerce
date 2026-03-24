# Wishlist API Response Update

## Changes Made

Updated the `/api/wishlist` endpoint to return the same response structure as `/api/products` for consistency.

## Before (Old Response)

```json
{
  "status": true,
  "message": "Operation completed successfully.",
  "errors": [],
  "data": [
    {
      "id": 123,
      "vendor_product": {
        "id": 4952,
        "name": "Product Name",
        "slug": "product-slug",
        // ... full vendor product object
      },
      "created_at": "2026-03-17T10:30:00.000000Z",
      "updated_at": "2026-03-17T10:30:00.000000Z"
    }
  ]
}
```

## After (New Response)

```json
{
  "status": true,
  "message": "Operation completed successfully.",
  "errors": [],
  "data": [
    {
      "id": 4952,
      "slug": "product-slug",
      "name": "Product Name",
      "image": "http://example.com/storage/products/image.jpg",
      "points": 300,
      "sku": "SKU-123",
      "status": "Active",
      "is_fav": true,
      "reviews_count": 5,
      "review_avg_star": 4.5,
      "price_before_taxes": "300.00",
      "real_price": "345.00",
      "fake_price": "400.00",
      "discount": "10%",
      "remaining_stock": 100,
      "vendor": {
        "id": 1,
        "name": "Vendor Name",
        "slug": "vendor-slug"
      },
      "brand": {
        "id": 10,
        "title": "Brand Name",
        "slug": "brand-slug"
      },
      "wishlist_id": 123,
      "added_at": "17 Mar, 2026, 10:30 AM"
    }
  ]
}
```

## Benefits

1. **Consistency**: Same structure as products listing makes frontend integration easier
2. **Performance**: Lightweight response with only essential data
3. **Compatibility**: Can reuse the same product card component for both products and wishlist
4. **Additional Info**: Includes `wishlist_id` for easy removal and `added_at` timestamp

## API Endpoint

```
GET /api/wishlist
```

### Query Parameters

- `per_page` (optional): Number of items per page (default: 15)
- `paginated` (optional): Enable/disable pagination (default: true)

### Headers

```
Authorization: Bearer {token}
Accept: application/json
```

## Files Modified

1. `Modules/Order/app/Http/Resources/Api/WishlistResource.php`
   - Updated to match ProductListResource structure
   - Added price calculations with taxes
   - Added discount and fake price support
   - Added vendor and brand information
   - Added wishlist_id and added_at fields

## Testing

Test the endpoint:

```bash
curl -X GET "http://127.0.0.1:8000/api/wishlist" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

## Notes

- The `is_fav` field is always `true` in wishlist (since all items are favorites)
- The `wishlist_id` field is the ID of the wishlist entry (for removal)
- The `added_at` field shows when the item was added to wishlist
- All price calculations include taxes automatically
