# Product Check Availability API Endpoint

## Overview
Created a new API endpoint that accepts an array of product IDs and returns only the IDs that exist in the database (available products).

## Endpoint Details

**URL**: `POST /api/products/check-availability`

**Method**: POST

**Authentication**: Not required (public endpoint)

**Rate Limiting**: Uses the 'products' throttle group (same as other product endpoints)

## Request

### Headers
```
Content-Type: application/json
Accept: application/json
```

### Body
```json
{
  "product_ids": [1, 2, 3, 4, 5, 999, 1000]
}
```

### Validation Rules
- `product_ids`: required, must be an array
- `product_ids.*`: each element must be an integer

## Response

### Success Response (200 OK)
```json
{
  "message": "Success message in current locale",
  "success": true,
  "data": {
    "available_ids": [1, 2, 3, 4, 5],
    "total_requested": 7,
    "total_available": 5
  },
  "errors": [],
  "status": 200
}
```

### Validation Error Response (422 Unprocessable Entity)
```json
{
  "message": "Validation error message in current locale",
  "success": false,
  "data": [],
  "errors": {
    "product_ids": ["Product IDs array is required"]
  },
  "status": 422
}
```

## Implementation Details

### Files Modified

1. **Controller**: `Modules/CatalogManagement/app/Http/Controllers/Api/ProductApiController.php`
   - Added `checkAvailability()` method
   - Validates request
   - Calls service method
   - Returns formatted response

2. **Service**: `Modules/CatalogManagement/app/Services/Api/ProductApiService.php`
   - Added `getAvailableProductIds()` method
   - Delegates to repository

3. **Repository**: `Modules/CatalogManagement/app/Repositories/Api/ProductApiRepository.php`
   - Added `getAvailableProductIds()` method
   - Queries database using `whereIn()` and `pluck()`
   - Uses `withoutGlobalScopes()` to bypass country filtering

4. **Interface**: `Modules/CatalogManagement/app/Interfaces/Api/ProductApiRepositoryInterface.php`
   - Added method signature

5. **Routes**: `Modules/CatalogManagement/routes/api.php`
   - Added POST route: `/products/check-availability`

## Use Cases

1. **Cart Validation**: Frontend can check if products in cart still exist before checkout
2. **Wishlist Sync**: Verify wishlist items are still available
3. **Bulk Operations**: Validate multiple product IDs before performing operations
4. **Data Integrity**: Ensure product references are valid

## Example Usage

### JavaScript/Fetch
```javascript
const productIds = [1, 2, 3, 4, 5, 999];

fetch('http://127.0.0.1:8000/api/products/check-availability', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  body: JSON.stringify({
    product_ids: productIds
  })
})
.then(response => response.json())
.then(data => {
  console.log('Available IDs:', data.data.available_ids);
  console.log('Total requested:', data.data.total_requested);
  console.log('Total available:', data.data.total_available);
})
.catch(error => console.error('Error:', error));
```

### cURL
```bash
curl -X POST http://127.0.0.1:8000/api/products/check-availability \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"product_ids": [1, 2, 3, 4, 5, 999]}'
```

### Axios
```javascript
import axios from 'axios';

const checkAvailability = async (productIds) => {
  try {
    const response = await axios.post(
      'http://127.0.0.1:8000/api/products/check-availability',
      { product_ids: productIds }
    );
    
    return response.data.data.available_ids;
  } catch (error) {
    console.error('Error checking availability:', error);
    throw error;
  }
};

// Usage
const availableIds = await checkAvailability([1, 2, 3, 4, 5]);
```

## Performance Considerations

- Uses `pluck()` to return only IDs (minimal data transfer)
- Uses `whereIn()` for efficient batch querying
- No relationships loaded (fast query)
- Bypasses global scopes for maximum performance

## Testing

To test the endpoint:

1. **Valid Request**:
```bash
POST /api/products/check-availability
Body: {"product_ids": [1, 2, 3]}
Expected: Returns available IDs from the list
```

2. **Empty Array**:
```bash
POST /api/products/check-availability
Body: {"product_ids": []}
Expected: 422 validation error
```

3. **Invalid Data Type**:
```bash
POST /api/products/check-availability
Body: {"product_ids": ["abc", "def"]}
Expected: 422 validation error
```

4. **Non-existent IDs**:
```bash
POST /api/products/check-availability
Body: {"product_ids": [99999, 88888]}
Expected: Returns empty available_ids array
```

## Notes

- The endpoint uses `withoutGlobalScopes()` to check all products regardless of country
- If you need country-specific filtering, modify the repository method to include the country scope
- The endpoint is public and doesn't require authentication
- Rate limiting is applied via the 'products' throttle group
