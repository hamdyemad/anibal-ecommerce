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

        // Calculate registration trend by day (group by date only, not datetime)
        $registrationTrend = [];
        foreach ($allData as $customer) {
            // Use getRawOriginal to bypass HumanDates trait accessor
            $rawCreatedAt = $customer->getRawOriginal('created_at');
            if ($rawCreatedAt) {
                $date = \Carbon\Carbon::parse($rawCreatedAt)->format('Y-m-d');
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
     * Get area users report (Customers by customer_addresses table)
     */
    public function getAreaUsersReport(ReportFilterDTO $filter): array
    {
        // Get current country from session
        $countryCode = session('country_code');
        $countryId = $countryCode
            ? \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->value('id')
            : null;

        // Start with customers query - filter by customer_addresses table
        $query = Customer::query();

        // Filter by customer_addresses table
        if ($filter->city_id) {
            // Filter customers who have an address in the selected city
            $query->whereHas('addresses', function ($q) use ($filter) {
                $q->where('city_id', $filter->city_id);
            });
        } elseif ($countryId) {
            // Filter customers who have an address in the current country
            $query->whereHas('addresses', function ($q) use ($countryId) {
                $q->where('country_id', $countryId);
            });
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

        // Get all data for statistics and charts - load addresses with city
        $allData = $query->with(['addresses.city'])->get();

        // Calculate statistics
        $activeCount = $allData->where('status', 1)->count();
        $inactiveCount = $allData->where('status', 0)->count();

        // Calculate registration trend by day (group by date only, not datetime)
        $registrationTrend = [];
        foreach ($allData as $customer) {
            // Use getRawOriginal to bypass HumanDates trait accessor
            $rawCreatedAt = $customer->getRawOriginal('created_at');
            if ($rawCreatedAt) {
                $date = \Carbon\Carbon::parse($rawCreatedAt)->format('Y-m-d');
                $registrationTrend[$date] = ($registrationTrend[$date] ?? 0) + 1;
            }
        }
        ksort($registrationTrend);

        // Calculate city distribution - count unique customers per city from customer_addresses
        // Query directly from customer_addresses with all filters applied
        $addressQuery = \Modules\Customer\app\Models\CustomerAddress::query()
            ->select('customer_addresses.city_id')
            ->selectRaw('COUNT(DISTINCT customer_addresses.customer_id) as customer_count')
            ->join('customers', 'customers.id', '=', 'customer_addresses.customer_id')
            ->whereNotNull('customer_addresses.city_id')
            ->groupBy('customer_addresses.city_id');
        
        // Apply city filter
        if ($filter->city_id) {
            $addressQuery->where('customer_addresses.city_id', $filter->city_id);
        }
        
        // Apply country filter
        if ($countryId) {
            $addressQuery->where('customer_addresses.country_id', $countryId);
        }
        
        // Apply date range filter on customer created_at
        if ($filter->from) {
            $addressQuery->whereDate('customers.created_at', '>=', $filter->from);
        }
        if ($filter->to) {
            $addressQuery->whereDate('customers.created_at', '<=', $filter->to);
        }
        
        // Apply status filter
        if ($filter->status) {
            $addressQuery->where('customers.status', $filter->status === 'active' ? 1 : 0);
        }
        
        // Apply search filter
        if ($filter->search) {
            $addressQuery->where(function ($q) use ($filter) {
                $q->where('customers.first_name', 'like', '%' . $filter->search . '%')
                  ->orWhere('customers.last_name', 'like', '%' . $filter->search . '%')
                  ->orWhere('customers.email', 'like', '%' . $filter->search . '%')
                  ->orWhere('customers.phone', 'like', '%' . $filter->search . '%');
            });
        }
        
        $cityCounts = $addressQuery->get();
        
        $cityDistribution = [];
        foreach ($cityCounts as $item) {
            $city = \Modules\AreaSettings\app\Models\City::find($item->city_id);
            $cityName = $city?->name ?? 'Unknown';
            $cityDistribution[$cityName] = (int) $item->customer_count;
        }
        arsort($cityDistribution); // Sort by count descending

        // Now paginate for table display
        $data = $query->with(['addresses.city'])
            ->paginate(
                perPage: $filter->per_page,
                page: $filter->page,
                columns: ['id', 'first_name', 'last_name', 'email', 'phone', 'status', 'created_at']
            );

        // Add index and city name to each item
        $items = $data->items();
        $startIndex = ($filter->page - 1) * $filter->per_page + 1;
        foreach ($items as $index => $item) {
            $item->index = $startIndex + $index;
            $item->name = trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? '')) ?: 'N/A';
            
            // Count addresses per city for this customer
            $cityCounts = [];
            foreach ($item->addresses as $address) {
                $cityName = $address->city?->name ?? 'Unknown';
                $cityCounts[$cityName] = ($cityCounts[$cityName] ?? 0) + 1;
            }
            
            // Format as "1 Cairo, 2 Alex"
            $cityParts = [];
            foreach ($cityCounts as $cityName => $count) {
                $cityParts[] = $count . ' ' . $cityName;
            }
            $item->city_name = !empty($cityParts) ? implode(', ', $cityParts) : 'N/A';
            $item->address_count = $item->addresses->count();
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
        $isVendor = isVendor();
        $vendorId = null;
        if ($isVendor) {
            $vendor = auth()->user()->vendor;
            $vendorId = $vendor ? $vendor->id : null;
        }

        $query = Order::withoutCountryFilter();

        // Apply country filter manually with qualified table name
        $countryCode = session('country_code') ?? request('country_code') ?? config('app.default_country_code');
        if ($countryCode) {
            $countryId = \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->value('id');
            if ($countryId) {
                $query->where('orders.country_id', $countryId);
            }
        }

        // If user is vendor, filter to show only their orders
        if ($isVendor && $vendorId) {
            $query->whereHas('products', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            });
        } elseif ($isVendor) {
            // If vendor user but no vendor record, return empty results
            $query->whereRaw('1 = 0');
        }

        // Date range filter
        if ($filter->from) {
            $query->whereDate('orders.created_at', '>=', $filter->from);
        }
        if ($filter->to) {
            $query->whereDate('orders.created_at', '<=', $filter->to);
        }

        // Stage filter - for vendors, filter by vendor_order_stages
        if ($filter->type) {
            if ($isVendor && $vendorId) {
                $query->whereHas('vendorStages', function ($q) use ($filter, $vendorId) {
                    $q->where('vendor_id', $vendorId)
                      ->where('stage_id', $filter->type);
                });
            } else {
                $query->where('stage_id', $filter->type);
            }
        }

        // Search filter (by order number or customer name)
        if ($filter->search) {
            $query->where(function ($q) use ($filter) {
                $q->where('order_number', 'like', '%' . $filter->search . '%')
                  ->orWhereHas('customer', function ($customerQ) use ($filter) {
                      $customerQ->where('first_name', 'like', '%' . $filter->search . '%')
                                ->orWhere('last_name', 'like', '%' . $filter->search . '%')
                                ->orWhere('email', 'like', '%' . $filter->search . '%')
                                ->orWhere('phone', 'like', '%' . $filter->search . '%');
                  });
            });
        }

        $total = $query->count();

        // Get all data for charts before pagination (without ordering to avoid GROUP BY issues)
        $allData = clone $query;

        // Calculate stage distribution based on user type
        if ($isVendor && $vendorId) {
            // For vendors, use vendor_order_stages
            $stageDistribution = \DB::table('vendor_order_stages')
                ->join('order_stages', 'vendor_order_stages.stage_id', '=', 'order_stages.id')
                ->whereIn('vendor_order_stages.order_id', $allData->clone()->pluck('orders.id'))
                ->where('vendor_order_stages.vendor_id', $vendorId)
                ->select('order_stages.type', \DB::raw('COUNT(*) as count'))
                ->groupBy('order_stages.type')
                ->get()
                ->pluck('count', 'type')
                ->toArray();
        } else {
            $stageDistribution = $allData->clone()
                ->join('order_stages', 'orders.stage_id', '=', 'order_stages.id')
                ->select('order_stages.type', \DB::raw('COUNT(orders.id) as count'))
                ->groupBy('order_stages.type')
                ->get()
                ->pluck('count', 'type')
                ->toArray();
        }

        // Calculate orders trend (by date) - use raw query to avoid GROUP BY issues
        $orderIds = $allData->clone()->pluck('orders.id');
        $ordersTrend = \DB::table('orders')
            ->whereIn('id', $orderIds)
            ->selectRaw('DATE(created_at) as date, COUNT(id) as count')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Calculate completed and pending counts based on user type
        if ($isVendor && $vendorId) {
            $completedCount = \DB::table('vendor_order_stages')
                ->join('order_stages', 'vendor_order_stages.stage_id', '=', 'order_stages.id')
                ->whereIn('vendor_order_stages.order_id', $orderIds)
                ->where('vendor_order_stages.vendor_id', $vendorId)
                ->where('order_stages.type', 'deliver')
                ->count();

            $pendingCount = \DB::table('vendor_order_stages')
                ->join('order_stages', 'vendor_order_stages.stage_id', '=', 'order_stages.id')
                ->whereIn('vendor_order_stages.order_id', $orderIds)
                ->where('vendor_order_stages.vendor_id', $vendorId)
                ->whereIn('order_stages.type', ['new', 'in_progress'])
                ->count();
        } else {
            $completedCount = \DB::table('orders')
                ->join('order_stages', 'orders.stage_id', '=', 'order_stages.id')
                ->whereIn('orders.id', $orderIds)
                ->where('order_stages.type', 'deliver')
                ->count();

            $pendingCount = \DB::table('orders')
                ->join('order_stages', 'orders.stage_id', '=', 'order_stages.id')
                ->whereIn('orders.id', $orderIds)
                ->whereIn('order_stages.type', ['new', 'in_progress'])
                ->count();
        }

        // Now add ordering for pagination
        $query->orderBy('orders.created_at', 'desc');

        // Load relationships based on user type
        $relationships = ['customer', 'stage', 'products'];
        if ($isVendor && $vendorId) {
            $relationships[] = 'vendorStages.stage';
        }

        $data = $query->with($relationships)
            ->paginate(
                perPage: $filter->per_page,
                page: $filter->page
            );

        // Transform data for frontend
        $items = $data->items();
        $transformedItems = [];
        foreach ($items as $index => $order) {
            $customerName = 'N/A';
            if ($order->customer) {
                $customerName = trim(($order->customer->first_name ?? '') . ' ' . ($order->customer->last_name ?? '')) ?: 'N/A';
            }
            
            // Calculate total and get stage based on user type
            $orderTotal = $order->total_price;
            $stageName = $order->stage ? $order->stage->name : 'N/A';
            $stageType = $order->stage ? $order->stage->type : null;
            
            if ($isVendor && $vendorId) {
                // For vendors, show only their products total + shipping
                $vendorProducts = $order->products->where('vendor_id', $vendorId);
                // Note: price field already contains total (unit_price * quantity)
                $productsTotal = $vendorProducts->sum('price');
                $shippingTotal = $vendorProducts->sum('shipping_cost');
                $orderTotal = $productsTotal + $shippingTotal;
                
                // Get vendor's stage from vendor_order_stages
                $vendorStage = $order->vendorStages->where('vendor_id', $vendorId)->first();
                if ($vendorStage && $vendorStage->stage) {
                    $stageName = $vendorStage->stage->name;
                    $stageType = $vendorStage->stage->type;
                }
            }
            
            $transformedItems[] = [
                'index' => ($filter->page - 1) * $filter->per_page + $index + 1,
                'order_number' => $order->order_number,
                'customer_name' => $customerName,
                'stage' => $stageName,
                'stage_type' => $stageType,
                'total' => $orderTotal,
                'created_at' => $order->created_at,
            ];
        }

        return [
            'total' => $total,
            'count' => $data->count(),
            'per_page' => $filter->per_page,
            'current_page' => $filter->page,
            'last_page' => $data->lastPage(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
            'data' => $transformedItems,
            'statistics' => [
                'stage_distribution' => $stageDistribution,
                'orders_trend' => $ordersTrend,
                'completed' => $completedCount,
                'pending' => $pendingCount,
                'total_filtered' => $total,
            ],
        ];
    }

    /**
     * Get products report
     */
    public function getProductsReport(ReportFilterDTO $filter): array
    {
        $query = \Modules\CatalogManagement\app\Models\VendorProduct::withoutCountryFilter();

        // Apply country filter manually
        $countryCode = session('country_code') ?? request('country_code') ?? config('app.default_country_code');
        if ($countryCode) {
            $countryId = \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->value('id');
            if ($countryId) {
                $query->where('vendor_products.country_id', $countryId);
            }
        }

        // If user is vendor, filter to show only their products
        if (isVendor()) {
            $vendor = auth()->user()->vendor;
            if ($vendor) {
                $query->where('vendor_id', $vendor->id);
            } else {
                // If vendor user but no vendor record, return empty results
                $query->whereRaw('1 = 0');
            }
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
            $query->where('status', $filter->status);
        }

        // Category filter
        if ($filter->category) {
            $query->whereHas('product', function ($q) use ($filter) {
                $q->where('category_id', $filter->category);
            });
        }

        // Vendor filter
        if ($filter->vendor) {
            $query->where('vendor_id', $filter->vendor);
        }

        // Search filter
        if ($filter->search) {
            $searchTerm = strtolower($filter->search);
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(sku) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereRaw('LOWER(status) LIKE ?', ['%' . $searchTerm . '%'])
                  ->orWhereHas('product', function ($productQ) use ($searchTerm) {
                      $productQ->whereHas('translations', function ($transQ) use ($searchTerm) {
                          $transQ->whereRaw('LOWER(lang_value) LIKE ?', ['%' . $searchTerm . '%'])
                                 ->where('lang_key', 'title');
                      });
                  });
            });
        }

        $total = $query->count();

        // Get all data for statistics
        $allData = clone $query;

        // Calculate status distribution
        $statusDistribution = $allData->clone()
            ->select('status', \DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Calculate products trend (by date)
        $productsTrend = $allData->clone()
            ->selectRaw('DATE(created_at) as date, COUNT(id) as count')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Calculate active/inactive counts
        $activeCount = $allData->clone()->where('is_active', 1)->count();
        $inactiveCount = $allData->clone()->where('is_active', 0)->count();

        $data = $query->with(['product.category', 'product.mainImage', 'vendor.logo'])
            ->paginate(
                perPage: $filter->per_page,
                page: $filter->page
            );

        // Transform data for frontend
        $items = $data->items();
        $transformedItems = [];
        foreach ($items as $index => $vendorProduct) {
            $productImage = null;
            if ($vendorProduct->product && $vendorProduct->product->mainImage) {
                $productImage = asset('storage/' . $vendorProduct->product->mainImage->path);
            }
            
            $vendorImage = null;
            if ($vendorProduct->vendor && $vendorProduct->vendor->logo) {
                $vendorImage = asset('storage/' . $vendorProduct->vendor->logo->path);
            }
            
            $transformedItems[] = [
                'index' => ($filter->page - 1) * $filter->per_page + $index + 1,
                'name' => $vendorProduct->product ? $vendorProduct->product->name : 'N/A',
                'product_image' => $productImage,
                'sku' => $vendorProduct->sku,
                'category' => $vendorProduct->product?->category ? $vendorProduct->product->category->name : 'N/A',
                'vendor' => $vendorProduct->vendor ? $vendorProduct->vendor->name : 'N/A',
                'vendor_image' => $vendorImage,
                'status' => $vendorProduct->status,
                'is_active' => $vendorProduct->is_active,
                'sales' => $vendorProduct->sales,
                'views' => $vendorProduct->views,
                'created_at' => $vendorProduct->created_at,
            ];
        }

        return [
            'total' => $total,
            'count' => $data->count(),
            'per_page' => $filter->per_page,
            'current_page' => $filter->page,
            'last_page' => $data->lastPage(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
            'data' => $transformedItems,
            'statistics' => [
                'status_distribution' => $statusDistribution,
                'products_trend' => $productsTrend,
                'active' => $activeCount,
                'inactive' => $inactiveCount,
                'total_filtered' => $total,
            ],
        ];
    }

    /**
     * Get points report
     */
    public function getPointsReport(ReportFilterDTO $filter): array
    {
        $query = \Modules\SystemSetting\app\Models\UserPoints::query()
            ->whereHas('user'); // Only include user_points with valid users

        // Apply country filter through user relationship (only if users have country_id)
        $countryCode = session('country_code') ?? request('country_code') ?? config('app.default_country_code');
        if ($countryCode) {
            $countryId = \Modules\AreaSettings\app\Models\Country::where('code', $countryCode)->value('id');
            if ($countryId) {
                $query->whereHas('user', function($q) use ($countryId) {
                    $q->where(function($subQ) use ($countryId) {
                        $subQ->where('country_id', $countryId)
                             ->orWhereNull('country_id'); // Include users without country
                    });
                });
            }
        }

        // Date range filter
        if ($filter->from) {
            $query->whereDate('created_at', '>=', $filter->from);
        }
        if ($filter->to) {
            $query->whereDate('created_at', '<=', $filter->to);
        }

        // Search filter
        if ($filter->search) {
            $query->whereHas('user', function ($q) use ($filter) {
                $q->where('email', 'like', '%' . $filter->search . '%');
            });
        }

        $total = $query->count();

        // Get all data for statistics
        $allData = clone $query;

        // Calculate points distribution
        $pointsDistribution = [
            'high' => $allData->clone()->where('total_points', '>=', 1000)->count(),
            'medium' => $allData->clone()->whereBetween('total_points', [100, 999])->count(),
            'low' => $allData->clone()->where('total_points', '<', 100)->count(),
        ];

        // Calculate points trend (by date)
        $pointsTrend = $allData->clone()
            ->selectRaw('DATE(created_at) as date, SUM(total_points) as total')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        // Calculate totals
        $totalPoints = $allData->clone()->sum('total_points');
        $totalEarned = $allData->clone()->sum('earned_points');
        $totalRedeemed = $allData->clone()->sum('redeemed_points');

        $data = $query->with('user')
            ->paginate(
                perPage: $filter->per_page,
                page: $filter->page
            );

        // Transform data for frontend
        $items = $data->items();
        $transformedItems = [];
        foreach ($items as $index => $userPoint) {
            $transformedItems[] = [
                'index' => ($filter->page - 1) * $filter->per_page + $index + 1,
                'user_name' => $userPoint->user?->name ?: ($userPoint->user?->email ?? 'N/A'),
                'email' => $userPoint->user?->email ?? 'N/A',
                'total_points' => $userPoint->total_points,
                'earned_points' => $userPoint->earned_points,
                'redeemed_points' => $userPoint->redeemed_points,
                'points_spent' => $userPoint->redeemed_points, // Same as redeemed
                'remaining_points' => $userPoint->total_points, // Current total points
                'created_at' => $userPoint->created_at,
            ];
        }

        // Calculate totals for all users (not just filtered)
        $allUsersQuery = \Modules\SystemSetting\app\Models\UserPoints::query()->whereHas('user');

        $totalUsers = $allUsersQuery->count();
        $totalEarned = $allUsersQuery->sum('earned_points');
        $totalRedeemed = $allUsersQuery->sum('redeemed_points');
        $avgPoints = $totalUsers > 0 ? $totalEarned / $totalUsers : 0;

        return [
            'total' => $total,
            'count' => $data->count(),
            'per_page' => $filter->per_page,
            'current_page' => $filter->page,
            'last_page' => $data->lastPage(),
            'from' => $data->firstItem(),
            'to' => $data->lastItem(),
            'data' => $transformedItems,
            'statistics' => [
                'total_users' => $totalUsers,
                'total_earned' => $totalEarned,
                'total_redeemed' => $totalRedeemed,
                'avg_points' => round($avgPoints, 2),
                'points_distribution' => $pointsDistribution,
                'points_trend' => $pointsTrend,
            ]
        ];
    }

    /**
     * Get profitability report
     */
    public function getProfitabilityReport(ReportFilterDTO $filter): array
    {
        $dateFrom = $filter->from ?? now()->startOfMonth();
        $dateTo = $filter->to ?? now()->endOfMonth();
        
        // Get delivered orders in date range
        $orders = Order::withoutCountryFilter()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereHas('stage', function($q) {
                $q->where('type', 'deliver');
            })
            ->with(['orderProducts'])
            ->get();
        
        // Calculate metrics
        $totalRevenue = $orders->sum('total_price');
        $totalCosts = $orders->sum(function($order) {
            return $order->orderProducts->sum('commission');
        });
        $netProfit = $totalRevenue - $totalCosts;
        $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;
        $ordersCount = $orders->count();
        $averageOrderValue = $ordersCount > 0 ? $totalRevenue / $ordersCount : 0;
        
        // Get previous period for comparison
        $periodLength = $dateFrom->diffInDays($dateTo);
        $prevDateFrom = $dateFrom->copy()->subDays($periodLength);
        $prevDateTo = $dateFrom->copy()->subDay();
        
        $prevOrders = Order::withoutCountryFilter()
            ->whereBetween('created_at', [$prevDateFrom, $prevDateTo])
            ->whereHas('stage', function($q) {
                $q->where('type', 'deliver');
            })
            ->with(['orderProducts'])
            ->get();
        
        $prevRevenue = $prevOrders->sum('total_price');
        $prevCosts = $prevOrders->sum(function($order) {
            return $order->orderProducts->sum('commission');
        });
        $prevProfit = $prevRevenue - $prevCosts;
        $growthRate = $prevRevenue > 0 ? (($totalRevenue - $prevRevenue) / $prevRevenue) * 100 : 0;
        
        return [
            'kpis' => [
                'total_revenue' => round($totalRevenue, 2),
                'total_costs' => round($totalCosts, 2),
                'net_profit' => round($netProfit, 2),
                'profit_margin' => round($profitMargin, 2),
                'orders_count' => $ordersCount,
                'average_order_value' => round($averageOrderValue, 2),
                'growth_rate' => round($growthRate, 2),
            ],
            'comparison' => [
                'current_period' => [
                    'revenue' => round($totalRevenue, 2),
                    'costs' => round($totalCosts, 2),
                    'profit' => round($netProfit, 2),
                ],
                'previous_period' => [
                    'revenue' => round($prevRevenue, 2),
                    'costs' => round($prevCosts, 2),
                    'profit' => round($prevProfit, 2),
                ],
            ],
            'monthly_trend' => $this->getMonthlyProfitTrend($dateFrom, $dateTo),
        ];
    }

    /**
     * Get monthly profit trend
     */
    private function getMonthlyProfitTrend($dateFrom, $dateTo): array
    {
        $months = [];
        $current = $dateFrom->copy()->startOfMonth();
        
        while ($current <= $dateTo) {
            $monthOrders = Order::withoutCountryFilter()
                ->whereYear('created_at', $current->year)
                ->whereMonth('created_at', $current->month)
                ->whereHas('stage', function($q) {
                    $q->where('type', 'deliver');
                })
                ->with(['orderProducts'])
                ->get();
            
            $revenue = $monthOrders->sum('total_price');
            $costs = $monthOrders->sum(function($o) {
                return $o->orderProducts->sum('commission');
            });
            
            $months[] = [
                'month' => $current->format('M Y'),
                'revenue' => round($revenue, 2),
                'costs' => round($costs, 2),
                'profit' => round($revenue - $costs, 2),
            ];
            
            $current->addMonth();
        }
        
        return $months;
    }

    /**
     * Get sales analysis report
     */
    public function getSalesAnalysisReport(ReportFilterDTO $filter): array
    {
        $dateFrom = $filter->from ?? now()->startOfMonth();
        $dateTo = $filter->to ?? now()->endOfMonth();
        
        // Get all orders in date range
        $orders = Order::withoutCountryFilter()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['stage', 'orderProducts'])
            ->get();
        
        // Calculate metrics
        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total_price');
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        
        // Orders by stage
        $ordersByStage = $orders->groupBy(function($order) {
            return $order->stage ? $order->stage->name : 'Unknown';
        })->map(function($group) {
            return $group->count();
        })->toArray();
        
        // Daily sales trend
        $dailySales = $orders->groupBy(function($order) {
            return $order->created_at->format('Y-m-d');
        })->map(function($group) {
            return [
                'orders' => $group->count(),
                'revenue' => round($group->sum('total_price'), 2),
            ];
        })->toArray();
        
        // Top selling days
        $topDays = collect($dailySales)->sortByDesc('revenue')->take(5)->toArray();
        
        return [
            'kpis' => [
                'total_orders' => $totalOrders,
                'total_revenue' => round($totalRevenue, 2),
                'average_order_value' => round($averageOrderValue, 2),
            ],
            'orders_by_stage' => $ordersByStage,
            'daily_sales' => $dailySales,
            'top_days' => $topDays,
        ];
    }

    /**
     * Get product performance report
     */
    public function getProductPerformanceReport(ReportFilterDTO $filter): array
    {
        $dateFrom = $filter->from ?? now()->startOfMonth();
        $dateTo = $filter->to ?? now()->endOfMonth();
        
        // Get order products in date range
        $orderProducts = \Modules\Order\app\Models\OrderProduct::query()
            ->whereHas('order', function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('created_at', [$dateFrom, $dateTo]);
            })
            ->with(['vendorProduct.product', 'order'])
            ->get();
        
        // Group by product
        $productStats = $orderProducts->groupBy('vendor_product_id')->map(function($group) {
            $product = $group->first()->vendorProduct;
            $productName = $product && $product->product ? $product->product->name : 'Unknown';
            
            return [
                'product_name' => $productName,
                'quantity_sold' => $group->sum('quantity'),
                'revenue' => round($group->sum('price'), 2),
                'orders_count' => $group->count(),
            ];
        })->values();
        
        // Sort by revenue
        $topProducts = $productStats->sortByDesc('revenue')->take(10)->values()->toArray();
        $lowProducts = $productStats->sortBy('revenue')->take(10)->values()->toArray();
        
        // Calculate totals
        $totalProducts = $productStats->count();
        $totalRevenue = $productStats->sum('revenue');
        $totalQuantity = $productStats->sum('quantity_sold');
        
        return [
            'kpis' => [
                'total_products' => $totalProducts,
                'total_revenue' => round($totalRevenue, 2),
                'total_quantity' => $totalQuantity,
            ],
            'top_products' => $topProducts,
            'low_products' => $lowProducts,
        ];
    }

    /**
     * Get customer analysis report
     */
    public function getCustomerAnalysisReport(ReportFilterDTO $filter): array
    {
        $dateFrom = $filter->from ?? now()->startOfMonth();
        $dateTo = $filter->to ?? now()->endOfMonth();
        
        // Get orders with customers in date range
        $orders = Order::withoutCountryFilter()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with(['customer'])
            ->get();
        
        // Group by customer
        $customerStats = $orders->groupBy('customer_id')->map(function($group) {
            $customer = $group->first()->customer;
            $customerName = $customer ? ($customer->first_name . ' ' . $customer->last_name) : 'Guest';
            
            return [
                'customer_name' => $customerName,
                'orders_count' => $group->count(),
                'total_spent' => round($group->sum('total_price'), 2),
                'average_order' => round($group->avg('total_price'), 2),
            ];
        })->values();
        
        // Sort by total spent
        $topCustomers = $customerStats->sortByDesc('total_spent')->take(10)->values()->toArray();
        
        // Calculate metrics
        $totalCustomers = $customerStats->count();
        $totalRevenue = $customerStats->sum('total_spent');
        $averageCustomerValue = $totalCustomers > 0 ? $totalRevenue / $totalCustomers : 0;
        
        // New vs returning customers
        $newCustomers = Customer::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $returningCustomers = $totalCustomers - $newCustomers;
        
        return [
            'kpis' => [
                'total_customers' => $totalCustomers,
                'new_customers' => $newCustomers,
                'returning_customers' => max(0, $returningCustomers),
                'average_customer_value' => round($averageCustomerValue, 2),
            ],
            'top_customers' => $topCustomers,
            'customer_distribution' => [
                'new' => $newCustomers,
                'returning' => max(0, $returningCustomers),
            ],
        ];
    }
}
