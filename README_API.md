# API Documentation Guide

## Quick Start

### For Developers
Our API is fully documented using **Apidog**, providing interactive documentation, testing capabilities, and mock servers.

**Access Documentation:**
- **Apidog Project:** [(https://multivendor-bnaia.apidog.io/)]
- **OpenAPI Spec:** `${APP_URL}/public/api-docs/openapi.json`

### Authentication
```bash
# Login to get token
curl -X POST https://your-domain.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Use token in subsequent requests
curl -X GET https://your-domain.com/api/v1/orders \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Exporting OpenAPI Spec from Apidog

To keep the in-code documentation up-to-date:

1. **Open Apidog** and navigate to your project
2. **Click on Project Settings** (gear icon)
3. **Go to Export** section
4. **Select "OpenAPI 3.0"** format
5. **Choose JSON or YAML** format
6. **Export** and save to `/public/api-docs/openapi.json`
7. **Commit** the file to version control

### Automation (Optional)
You can automate this using Apidog's CLI or API:
```bash
# Example using Apidog CLI (if available)
apidog export --project-id YOUR_PROJECT_ID --format openapi --output public/api-docs/openapi.json
```

## API Endpoints Overview

### Base URL
```
Production: https://multivendor.bnaia.com/api/v1
Local: http://localhost:8000/api/v1
```

### Core Modules
- **Authentication** - `/api/v1/auth/*`
- **Orders** - `/api/v1/orders/*`
- **Refunds** - `/api/v1/refunds/*`
- **Cart** - `/api/v1/cart/*`
- **Wishlist** - `/api/v1/wishlist/*`
- **Products** - `/api/v1/products/*`
- **Customer** - `/api/v1/customers/*`
- **Points** - `/api/v1/points/*`
- **Notifications** - `/api/v1/notifications/*`

## Security Features

### Rate Limiting
| Endpoint | Limit |
|----------|-------|
| Login/Register | 5/min per IP |
| Password Reset | 3/hour per IP |
| OTP | 10/hour per IP |
| General API | 60/min per user |

### Authorization
- ✅ Order ownership validation
- ✅ Refund request authorization
- ✅ Customer data protection
- ✅ Vendor data isolation

## Testing

### Using Apidog
1. Open Apidog project
2. Select endpoint to test
3. Configure parameters
4. Click "Send" to test

### Using cURL
```bash
# Example: Get orders
curl -X GET "http://localhost:8000/api/v1/orders" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept-Language: en"
```

### Using Postman
1. Import OpenAPI spec from `/public/api-docs/openapi.json`
2. Set environment variables
3. Test endpoints

## Response Format

### Success Response
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Validation error"]
  }
}
```

## Support
- **Documentation:** `/docs/API_DOCUMENTATION.md`
- **Apidog Project:** [Your Apidog link]
- **Issues:** GitHub Issues

---
**Maintained by:** Development Team
**Last Updated:** January 2026
