# Report Module

The Report Module provides comprehensive reporting functionality for different entities in the e-RAMO system.

## Features

- Registered Users Report
- Area Users Report
- Orders Report
- Products Report
- Points Report

Each report supports:
- Date range filtering (from/to)
- Search functionality with entity-specific fields
- Status filtering
- Pagination

## API Endpoints

### 1. Registered Users Report

**Endpoint:** `GET /api/v1/reports/registered-users`

**Authentication:** Required (Bearer Token)

**Parameters:**
- `from` (optional): Start date (Y-m-d format)
- `to` (optional): End date (Y-m-d format)
- `search` (optional): Search by first name, last name, email, or phone
- `status` (optional): Filter by status (active/inactive)
- `gender` (optional): Filter by gender (male/female/other)
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 15, max: 100)

**Example Request:**
```
GET /api/v1/reports/registered-users?from=2025-01-01&to=2025-12-31&gender=male&status=active&page=1&per_page=20
```

**Response:**
```json
{
  "status": true,
  "message": "Operation completed successfully.",
  "data": {
    "total": 150,
    "count": 20,
    "per_page": 20,
    "current_page": 1,
    "last_page": 8,
    "from": 1,
    "to": 20,
    "data": [...]
  }
}
```

---

### 2. Area Users Report

**Endpoint:** `GET /api/v1/reports/area-users`

**Authentication:** Required (Bearer Token)

**Parameters:**
- `from` (optional): Start date (Y-m-d format)
- `to` (optional): End date (Y-m-d format)
- `search` (optional): Search by area name (English or Arabic)
- `status` (optional): Filter by status (active/inactive)
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 15, max: 100)

**Example Request:**
```
GET /api/v1/reports/area-users?search=Cairo&status=active&page=1
```

---

### 3. Orders Report

**Endpoint:** `GET /api/v1/reports/orders`

**Authentication:** Required (Bearer Token)

**Parameters:**
- `from` (optional): Start date (Y-m-d format)
- `to` (optional): End date (Y-m-d format)
- `search` (optional): Search by order number or customer name
- `type` (optional): Filter by order status
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 15, max: 100)

**Example Request:**
```
GET /api/v1/reports/orders?from=2025-01-01&type=completed&page=1
```

---

### 4. Products Report

**Endpoint:** `GET /api/v1/reports/products`

**Authentication:** Required (Bearer Token)

**Parameters:**
- `from` (optional): Start date (Y-m-d format)
- `to` (optional): End date (Y-m-d format)
- `search` (optional): Search by product name or SKU
- `status` (optional): Filter by status (active/inactive)
- `category` (optional): Filter by category ID
- `vendor` (optional): Filter by vendor ID
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 15, max: 100)

**Example Request:**
```
GET /api/v1/reports/products?category=5&vendor=2&status=active&page=1
```

---

### 5. Points Report

**Endpoint:** `GET /api/v1/reports/points`

**Authentication:** Required (Bearer Token)

**Parameters:**
- `from` (optional): Start date (Y-m-d format)
- `to` (optional): End date (Y-m-d format)
- `search` (optional): Search by customer name or email
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 15, max: 100)

**Example Request:**
```
GET /api/v1/reports/points?search=Ahmed&page=1&per_page=50
```

---

## Architecture

### Directory Structure
```
Modules/Report/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   └── ReportApiController.php
│   │   │   └── Web/
│   │   │       └── ReportController.php
│   │   └── Requests/
│   │       └── ReportFilterRequest.php
│   ├── Interfaces/
│   │   └── ReportRepositoryInterface.php
│   ├── Repositories/
│   │   └── ReportRepository.php
│   ├── Services/
│   │   └── ReportService.php
│   ├── DTOs/
│   │   └── ReportFilterDTO.php
│   └── Providers/
│       └── ReportServiceProvider.php
├── routes/
│   ├── api.php
│   └── web.php
└── resources/
    └── views/
```

### Components

1. **ReportFilterDTO** - Data Transfer Object for filter parameters
2. **ReportRepositoryInterface** - Interface defining all report methods
3. **ReportRepository** - Implementation handling database queries
4. **ReportService** - Service layer orchestrating repository calls
5. **ReportFilterRequest** - Form request validation
6. **ReportApiController** - API controller for endpoints
7. **ReportController** - Web controller for views

## Validation Rules

All filter requests validate:
- `from` and `to`: Valid date format (Y-m-d)
- `to` must be >= `from`
- `status`: Only 'active' or 'inactive'
- `gender`: Only 'male', 'female', or 'other'
- `category` and `vendor`: Must exist in database
- `page` and `per_page`: Positive integers

## Error Handling

All endpoints return consistent error responses:

```json
{
  "status": false,
  "message": "Operation error message",
  "errors": {},
  "data": {
    "error": "Detailed error information"
  }
}
```

Status Code: 500
