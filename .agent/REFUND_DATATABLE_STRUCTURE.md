# Refund DataTable Structure - Complete ✅

## DataTable Response Format

### Endpoint
```
GET {lang}/{countryCode}/admin/refunds/datatable
```

### Request Parameters
```javascript
{
    "draw": 1,
    "start": 0,
    "length": 10,
    "search": "REF-20260119-0001",
    "status_filter": "pending",
    "created_date_from": "2026-01-01",
    "created_date_to": "2026-01-31"
}
```

### Response Structure
```json
{
    "draw": 1,
    "recordsTotal": 100,
    "recordsFiltered": 25,
    "data": [
        {
            "index": 1,
            "refund_info": "<div class='refund-info'>...</div>",
            "status": "<span class='badge badge-warning'>...</span>",
            "actions": "<div class='orderDatatable_actions'>...</div>"
        }
    ]
}
```

## Data Columns

### 1. Index Column
```json
{
    "index": 1
}
```
- Sequential number starting from pagination offset
- Type: Integer
- Display: Center aligned, bold

### 2. Refund Info Column
```html
<div class="refund-info">
    <div class="mb-1"><strong>Refund Number:</strong> REF-20260119-0001</div>
    <div class="mb-1"><strong>Order Number:</strong> ORD-000002</div>
    <div class="mb-1"><strong>Customer:</strong> John Doe</div>
    <div class="mb-1"><strong>Vendor:</strong> AGT</div> <!-- Only for admins -->
    <div class="mb-1"><strong>Total Refund Amount:</strong> 63.00 EGP</div>
    <div><strong>Created At:</strong> 2026-01-19 12:45</div>
</div>
```

**Contains:**
- Refund Number
- Order Number
- Customer Name
- Vendor Name (admin only)
- Total Refund Amount with currency
- Created At timestamp

### 3. Status Column
```html
<span class="badge badge-warning badge-round badge-lg">
    <i class="uil uil-clock"></i> Pending
</span>
```

**Status Badges:**
- `pending` → Yellow badge with clock icon
- `approved` → Blue badge with check icon
- `in_progress` → Primary badge with sync icon
- `picked_up` → Secondary badge with package icon
- `refunded` → Green badge with check-circle icon
- `rejected` → Red badge with times-circle icon

### 4. Actions Column
```html
<div class="orderDatatable_actions d-inline-flex gap-1 justify-content-center">
    <a href="/en/eg/admin/refunds/1" 
       class="view btn btn-sm btn-primary" 
       title="View">
        <i class="uil uil-eye m-0"></i>
    </a>
</div>
```

**Actions Available:**
- View button → Links to refund details page

## Filters Applied

### 1. Status Filter
```php
'status' => $request->status_filter ?? null
```
Filters by refund status (pending, approved, etc.)

### 2. Search Filter
```php
'search' => $request->search ?? null
```
Searches in refund number

### 3. Date Range Filter
```php
'date_from' => $request->created_date_from ?? null,
'date_to' => $request->created_date_to ?? null
```
Filters by creation date range

### 4. Vendor Filter (Non-Admin)
```php
if (!isAdmin()) {
    $filters['current_vendor_id'] = $vendor->id;
}
```
Automatically filters to show only vendor's refunds

## Translations Used

### English
```php
'refund::refund.fields.refund_number' => 'Refund Number'
'refund::refund.fields.order_number' => 'Order Number'
'refund::refund.fields.customer' => 'Customer'
'refund::refund.fields.vendor' => 'Vendor'
'refund::refund.fields.total_refund_amount' => 'Total Refund Amount'
'common.created_at' => 'Created At'
'common.view' => 'View'
'common.currency' => 'EGP'
```

### Arabic
```php
'refund::refund.fields.refund_number' => 'رقم الاسترجاع'
'refund::refund.fields.order_number' => 'رقم الطلب'
'refund::refund.fields.customer' => 'العميل'
'refund::refund.fields.vendor' => 'المورد'
'refund::refund.fields.total_refund_amount' => 'إجمالي المبلغ المسترجع'
'common.created_at' => 'تاريخ الإنشاء'
'common.view' => 'عرض'
'common.currency' => 'جنيه'
```

## DataTable Class Location

**File**: `Modules/Refund/app/DataTables/RefundRequestDataTable.php`

**Methods:**
- `handle(Request $request)` - Main handler
- `buildFilters(Request $request)` - Build filters array
- `formatData($refundRequests, int $start)` - Format data for DataTables
- `buildRefundInfo($refund)` - Build refund info HTML
- `buildStatusBadge(string $status)` - Build status badge HTML
- `buildActions($refund)` - Build actions HTML

## Controller Integration

**File**: `Modules/Refund/app/Http/Controllers/RefundRequestController.php`

```php
public function datatable(Request $request, RefundRequestDataTable $dataTable)
{
    return response()->json($dataTable->handle($request));
}
```

## View Configuration

**File**: `Modules/Refund/resources/views/refund-requests/index.blade.php`

```php
$headers = [
    ['label' => '#', 'class' => 'text-center'],
    ['label' => trans('refund::refund.titles.refund_details')],
    ['label' => trans('refund::refund.fields.status'), 'class' => 'text-center'],
    ['label' => trans('common.actions'), 'class' => 'text-center'],
];

$columns = [
    ['data' => 'index', 'orderable' => false, 'searchable' => false],
    ['data' => 'refund_info', 'orderable' => false, 'searchable' => false],
    ['data' => 'status', 'orderable' => false, 'searchable' => false],
    ['data' => 'actions', 'orderable' => false, 'searchable' => false],
];
```

## Example Full Response

```json
{
    "draw": 1,
    "recordsTotal": 1,
    "recordsFiltered": 1,
    "data": [
        {
            "index": 1,
            "refund_info": "<div class=\"refund-info\"><div class=\"mb-1\"><strong>Refund Number:</strong> REF-20260119-0001</div><div class=\"mb-1\"><strong>Order Number:</strong> ORD-000002</div><div class=\"mb-1\"><strong>Customer:</strong> Ahmed Ali</div><div class=\"mb-1\"><strong>Vendor:</strong> AGT</div><div class=\"mb-1\"><strong>Total Refund Amount:</strong> 63.00 EGP</div><div><strong>Created At:</strong> 2026-01-19 12:45</div></div>",
            "status": "<span class=\"badge badge-warning badge-round badge-lg\"><i class=\"uil uil-clock\"></i> Pending</span>",
            "actions": "<div class=\"orderDatatable_actions d-inline-flex gap-1 justify-content-center\"><a href=\"http://127.0.0.1:8000/en/eg/admin/refunds/1\" class=\"view btn btn-sm btn-primary\" title=\"View\"><i class=\"uil uil-eye m-0\"></i></a></div>"
        }
    ]
}
```

## Status

✅ **COMPLETE** - All translations are in place and datatable is fully functional
