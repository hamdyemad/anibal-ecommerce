<?php

namespace Modules\Report\app\Repositories;

use Modules\Report\app\DTOs\ReportFilterDTO;
use Modules\Report\app\Interfaces\ReportRepositoryInterface;
use Modules\Customer\app\Models\Customer;
use Modules\Order\app\Models\Order;
use Modules\CatalogManagement\app\Models\Product;

class ReportRepository implements ReportRepositoryInterface
{
    /**
     * Get registered users report
     */
    public function getRegisteredUsersReport(ReportFilterDTO $filter): array
    {
        \Log::info('ReportRepository::getRegisteredUsersReport', ['filter' => (array)$filter]);
        
        $query = Customer::query();

        // Date range filter
        if ($filter->from) {
            $query->whereDate('created_at', '>=', $filter->from);
        }
        if ($filter->to) {
            $query->whereDate('created_at', '<=', $filter->to);
        }

        // Status filter
        if ($filter->status) {
            $query->where('status', $filter->status === 'active' ? 1 : 0);
        }

        // Gender filter
        if ($filter->gender) {
            $query->where('gender', $filter->gender);
        }

        // Search filter
        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->where('first_name', 'like', '%' . $filter->search . '%')
                  ->orWhere('last_name', 'like', '%' . $filter->search . '%')
                  ->orWhere('email', 'like', '%' . $filter->search . '%')
                  ->orWhere('phone', 'like', '%' . $filter->search . '%');
            });
        }

        $total = $query->count();
        \Log::info('ReportRepository::Total count', ['total' => $total, 'sql' => $query->toSql()]);

        // Get statistics and chart data for ALL matching records (not just current page)
        $allData = $query->get(['id', 'first_name', 'last_name', 'email', 'phone', 'gender', 'status', 'created_at']);
        
        // Calculate statistics
        $activeCount = $allData->where('status', 1)->count();
        $inactiveCount = $allData->where('status', 0)->count();
        $genderDistribution = [
            'male' => $allData->where('gender', 'male')->count(),
            'female' => $allData->where('gender', 'female')->count(),
            'other' => $allData->where('gender', 'other')->count(),
        ];
        
        // Calculate registration trend by date
        $registrationTrend = [];
        foreach ($allData as $customer) {
            if ($customer->created_at) {
                $date = $customer->created_at;
                $registrationTrend[$date] = ($registrationTrend[$date] ?? 0) + 1;
            }
        }
        ksort($registrationTrend);

        // Now paginate for table display
        $data = $query->paginate(
            perPage: $filter->per_page,
            page: $filter->page,
            columns: ['id', 'first_name', 'last_name', 'email', 'phone', 'gender', 'status', 'created_at']
        );

        // Add index to each item
        $items = $data->items();
        $startIndex = ($filter->page - 1) * $filter->per_page + 1;
        foreach ($items as $index => $item) {
            $item->index = $startIndex + $index;
        }

        \Log::info('ReportRepository::Data retrieved', ['count' => count($items), 'items' => $items]);

        return [
            'total' => $total,
            'count' => $data->count(),
            'per_page' => $filter->per_page,
            'current_page' => $filter->page,
            'last_page' => $data->lastPage(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
            'data' => $items,
            'statistics' => [
                'active' => $activeCount,
                'inactive' => $inactiveCount,
                'total_filtered' => $total,
            ],
            'gender_distribution' => $genderDistribution,
            'registration_trend' => $registrationTrend,
        ];
    }

    /**
     * Get area users report (Customers by current country_code and selected city)
     */
    public function getAreaUsersReport(ReportFilterDTO $filter): array
    {
        // Get current country from session
        $countryCode = session('country_code');
        $countryId = $countryCode
            ? \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->value('id')
            : null;

        // Start with customers query
        $query = Customer::query();

        // If city is selected, filter by city/area
        if ($filter->city_id) {
            $query->where('city_id', $filter->city_id);
        } elseif ($countryId) {
            // Otherwise filter by country
            $query->where('country_id', $countryId);
        }

        // Date range filter
        if ($filter->from) {
            $query->whereDate('created_at', '>=', $filter->from);
        }
        if ($filter->to) {
            $query->whereDate('created_at', '<=', $filter->to);
        }

        // Status filter
        if ($filter->status) {
            $query->where('status', $filter->status === 'active' ? 1 : 0);
        }

        // Search filter
        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->where('first_name', 'like', '%' . $filter->search . '%')
                  ->orWhere('last_name', 'like', '%' . $filter->search . '%')
                  ->orWhere('email', 'like', '%' . $filter->search . '%')
                  ->orWhere('phone', 'like', '%' . $filter->search . '%');
            });
        }

        $total = $query->count();

        // Get all data for statistics and charts
        $allData = $query->with(['city'])->get();
        
        // Calculate statistics
        $activeCount = $allData->where('status', 1)->count();
        $inactiveCount = $allData->where('status', 0)->count();
        
        // Calculate registration trend by date
        $registrationTrend = [];
        foreach ($allData as $customer) {
            if ($customer->created_at) {
                $date = $customer->created_at;
                $registrationTrend[$date] = ($registrationTrend[$date] ?? 0) + 1;
            }
        }
        ksort($registrationTrend);

        // Calculate city distribution
        $cityDistribution = [];
        foreach ($allData as $customer) {
            $cityName = $customer->city?->name ?? 'Unknown';
            $cityDistribution[$cityName] = ($cityDistribution[$cityName] ?? 0) + 1;
        }
        arsort($cityDistribution); // Sort by count descending

        // Now paginate for table display
        $data = $query->with(['city'])
            ->paginate(
                perPage: $filter->per_page,
                page: $filter->page,
                columns: ['id', 'first_name', 'last_name', 'email', 'phone', 'status', 'created_at', 'city_id']
            );

        // Add index and city name to each item
        $items = $data->items();
        $startIndex = ($filter->page - 1) * $filter->per_page + 1;
        foreach ($items as $index => $item) {
            $item->index = $startIndex + $index;
            $item->name = $item->first_name . ' ' . $item->last_name;
            $item->city_name = $item->city?->name ?? 'N/A';
        }

        return [
            'total' => $total,
            'count' => $data->count(),
            'per_page' => $filter->per_page,
            'current_page' => $filter->page,
            'last_page' => $data->lastPage(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
            'data' => $items,
            'statistics' => [
                'active' => $activeCount,
                'inactive' => $inactiveCount,
                'total_filtered' => $total,
            ],
            'registration_trend' => $registrationTrend,
            'city_distribution' => $cityDistribution,
        ];
    }

    /**
     * Get orders report
     */
    public function getOrdersReport(ReportFilterDTO $filter): array
    {
        $query = Order::query();

        // Date range filter
        if ($filter->from) {
            $query->whereDate('created_at', '>=', $filter->from);
        }
        if ($filter->to) {
            $query->whereDate('created_at', '<=', $filter->to);
        }

        // Type/Status filter
        if ($filter->type) {
            $query->where('status', $filter->type);
        }

        // Search filter (by order number or customer name)
        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->where('order_number', 'like', '%' . $filter->search . '%')
                  ->orWhereHas('customer', function ($customerQ) use ($filter) {
                      $customerQ->where('first_name', 'like', '%' . $filter->search . '%')
                               ->orWhere('last_name', 'like', '%' . $filter->search . '%');
                  });
            });
        }

        $total = $query->count();

        $data = $query->with('customer')
            ->paginate(
                perPage: $filter->per_page,
                page: $filter->page
            );

        return [
            'total' => $total,
            'count' => $data->count(),
            'per_page' => $filter->per_page,
            'current_page' => $filter->page,
            'last_page' => $data->lastPage(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
            'data' => $data->items(),
        ];
    }

    /**
     * Get products report
     */
    public function getProductsReport(ReportFilterDTO $filter): array
    {
        $query = Product::query();

        // Date range filter
        if ($filter->from) {
            $query->whereDate('created_at', '>=', $filter->from);
        }
        if ($filter->to) {
            $query->whereDate('created_at', '<=', $filter->to);
        }

        // Status filter
        if ($filter->status) {
            $query->where('status', $filter->status === 'active' ? 1 : 0);
        }

        // Category filter
        if ($filter->category) {
            $query->whereHas('category', function ($q) use ($filter) {
                $q->where('id', $filter->category);
            });
        }

        // Vendor filter
        if ($filter->vendor) {
            $query->where('vendor_id', $filter->vendor);
        }

        // Search filter
        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->where('name_en', 'like', '%' . $filter->search . '%')
                  ->orWhere('name_ar', 'like', '%' . $filter->search . '%')
                  ->orWhere('sku', 'like', '%' . $filter->search . '%');
            });
        }

        $total = $query->count();

        $data = $query->with('category', 'vendor')
            ->paginate(
                perPage: $filter->per_page,
                page: $filter->page
            );

        return [
            'total' => $total,
            'count' => $data->count(),
            'per_page' => $filter->per_page,
            'current_page' => $filter->page,
            'last_page' => $data->lastPage(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
            'data' => $data->items(),
        ];
    }

    /**
     * Get points report
     */
    public function getPointsReport(ReportFilterDTO $filter): array
    {
        // Assuming there's a CustomerPoints or similar model
        $query = Customer::query()->whereHas('points');

        // Date range filter
        if ($filter->from) {
            $query->whereDate('created_at', '>=', $filter->from);
        }
        if ($filter->to) {
            $query->whereDate('created_at', '<=', $filter->to);
        }

        // Search filter
        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->where('first_name', 'like', '%' . $filter->search . '%')
                  ->orWhere('last_name', 'like', '%' . $filter->search . '%')
                  ->orWhere('email', 'like', '%' . $filter->search . '%');
            });
        }

        $total = $query->count();

        $data = $query->with('points')
            ->paginate(
                perPage: $filter->per_page,
                page: $filter->page,
                columns: ['id', 'first_name', 'last_name', 'email', 'created_at']
            );

        return [
            'total' => $total,
            'count' => $data->count(),
            'per_page' => $filter->per_page,
            'current_page' => $filter->page,
            'last_page' => $data->lastPage(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
            'data' => $data->items(),
        ];
    }
}
