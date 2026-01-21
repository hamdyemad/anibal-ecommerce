<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\UserType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\app\Models\AccountingEntry;
use Modules\Accounting\app\Models\Expense;
use Modules\AreaSettings\app\Models\Country;
use Modules\AreaSettings\app\Models\City;
use Modules\AreaSettings\app\Models\Region;
use Modules\CatalogManagement\app\Models\Product;
use Modules\CatalogManagement\app\Models\Tax;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\Promocode;
use Modules\CatalogManagement\app\Models\Review;
use Modules\Customer\app\Models\Customer;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderStage;
use Modules\Vendor\app\Models\Vendor;
use Modules\Vendor\app\Models\VendorRequest;
use Modules\SystemSetting\app\Models\Ad;
use Modules\SystemSetting\app\Models\Message;

class DashboardService
{
    protected $vendorId = null;
    protected $isVendor = false;

    protected function initVendorCheck()
    {
        // Check if authenticated user is a vendor
        if (Auth::check() && Auth::user()->isVendor()) {
            $this->isVendor = true;
            $this->vendorId = Auth::user()->vendor?->id;
        }
    }

    public function getDashboardData($countryCode)
    {
        // Initialize vendor check here instead of constructor
        $this->initVendorCheck();

        $stats = $this->getStats();
        $salesChart = $this->getSalesChartData();
        $earningsChart = $this->getEarningsChartData();
        $netSalesChart = $this->getNetSalesChartData();
        $latestOrders = $this->getLatestOrders();
        $topSellingProducts = $this->getTopSellingProducts();
        $topVendors = $this->isVendor ? [] : $this->getTopVendors(); // Hide for vendors
        $bestCustomers = $this->getBestCustomers();
        $ordersOverview = $this->getOrdersOverview();
        $salesOverview = $this->getSalesOverview();
        $incomeExpense = $this->getIncomeExpenseData();
        $recentActivities = $this->isVendor ? [] : $this->getRecentActivities(); // Hide for vendors

        return [
            'stats' => $stats,
            'salesChart' => $salesChart,
            'earningsChart' => $earningsChart,
            'netSalesChart' => $netSalesChart,
            'latestOrders' => $latestOrders,
            'topSellingProducts' => $topSellingProducts,
            'topVendors' => $topVendors,
            'bestCustomers' => $bestCustomers,
            'ordersOverview' => $ordersOverview,
            'salesOverview' => $salesOverview,
            'incomeExpense' => $incomeExpense,
            'recentActivities' => $recentActivities,
            'isVendor' => $this->isVendor,
        ];
    }

    private function getStats()
    {
        $today = Carbon::today();

        // If vendor, return vendor-specific stats
        if ($this->isVendor && $this->vendorId) {
            return $this->getVendorStats($today);
        }

        // Get admins count based on user type (same as admin management page)
        $adminsQuery = User::whereIn('user_type_id', [UserType::ADMIN_TYPE, UserType::VENDOR_USER_TYPE]);

        // Get delivered stage for earnings calculation
        $deliveredStage = OrderStage::withoutCountryFilter()->where('type', 'deliver')->first();
        $deliveredStageId = $deliveredStage ? $deliveredStage->id : null;

        // Calculate total_sales (earnings from delivered orders)
        // Sum order_products.price + shipping_cost for products in delivered stage
        $totalSales = $deliveredStageId
            ? \Modules\Order\app\Models\OrderProduct::query()
                ->join('orders', 'order_products.order_id', '=', 'orders.id')
                ->join('vendor_order_stages as vos', function ($join) {
                    $join->on('vos.order_id', '=', 'order_products.order_id')
                         ->on('vos.vendor_id', '=', 'order_products.vendor_id');
                })
                ->where('vos.stage_id', $deliveredStageId)
                ->selectRaw('SUM(order_products.price + COALESCE(order_products.shipping_cost, 0)) as total')
                ->value('total')
            : 0;

        // Calculate today_sales (earnings from delivered orders today)
        $todaySales = $deliveredStageId
            ? \Modules\Order\app\Models\OrderProduct::query()
                ->join('orders', 'order_products.order_id', '=', 'orders.id')
                ->join('vendor_order_stages as vos', function ($join) {
                    $join->on('vos.order_id', '=', 'order_products.order_id')
                         ->on('vos.vendor_id', '=', 'order_products.vendor_id');
                })
                ->where('vos.stage_id', $deliveredStageId)
                ->whereDate('orders.created_at', $today)
                ->selectRaw('SUM(order_products.price + COALESCE(order_products.shipping_cost, 0)) as total')
                ->value('total')
            : 0;

        return [
            // Users & Admins
            'total_admins' => $adminsQuery->count(),
            'vendor_users' => User::where('user_type_id', UserType::VENDOR_USER_TYPE)->count(),

            // Vendors
            'total_vendors' => Vendor::count(),
            'become_vendor_requests' => VendorRequest::where('status', 'pending')->count(),
            'accepted_vendors' => Vendor::where('active', 1)->count(),
            'rejected_vendors' => Vendor::where('active', 0)->count(),
            'new_vendors' => Vendor::whereDate('created_at', '>=', Carbon::now()->subDays(30))->count(),

            // Customers
            'total_customers' => Customer::count(),
            'total_male_users' => Customer::where('gender', 'male')->count(),
            'total_female_users' => Customer::where('gender', 'female')->count(),

            // Roles
            'admins_total_roles' => Role::where('type', 'admin')->count(),
            'vendor_users_total_roles' => Role::where('type', 'vendor')->count(),

            // Products & Stock
            'total_products' => Product::count(),
            'instock' => VendorProduct::whereHas('variants', function($q) {
                $q->whereHas('stocks', function($sq) {
                    $sq->where('quantity', '>', 0);
                });
            })->count(),
            'out_of_stock' => VendorProduct::where(function($q) {
                $q->whereDoesntHave('variants')
                  ->orWhereDoesntHave('variants.stocks', function($sq) {
                      $sq->where('quantity', '>', 0);
                  });
            })->count(),

            // Orders
            'total_orders' => Order::count(),
            'total_sales' => $totalSales,
            'today_sales' => $todaySales,
            'today_orders' => Order::whereDate('created_at', $today)->count(),
            'total_order_stages' => OrderStage::withoutCountryFilter()->count(),

            // Taxes
            'total_taxes' => Tax::count(),

            // Messages
            'total_messages' => Message::count(),

            // Promo Codes
            'promocodes' => Promocode::count(),

            // Area Settings
            'country' => Country::count(),
            'city' => City::count(),
            'region' => Region::count(),
            'subregion' => 0,

            // Offers
            'total_offers' => 0,

            // Reviews
            'all_products_reviews' => Review::count(),
            'accept_products_reviews' => Review::where('status', 'approved')->count(),
            'reject_products_reviews' => Review::where('status', 'rejected')->count(),

            // Advertisements
            'total_advertisments' => Ad::count(),

            // Refunds Statistics
            'refunds' => $this->getRefundStats(),
        ];
    }

    private function getVendorStats($today)
    {
        $vendorId = $this->vendorId;

        // Get delivered stage without country filter
        $deliveredStage = OrderStage::withoutCountryFilter()->where('type', 'deliver')->first();

        // Get order products for this vendor
        $orderProductsQuery = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId);

        // Get delivered order IDs for this vendor from vendor_order_stages table
        $deliveredOrderIds = $deliveredStage
            ? \Modules\Order\app\Models\VendorOrderStage::where('vendor_id', $vendorId)
                ->where('stage_id', $deliveredStage->id)
                ->pluck('order_id')
            : collect([]);

        // Get delivered order products using vendor-specific stage
        $deliveredOrderProducts = $deliveredStage && $deliveredOrderIds->isNotEmpty()
            ? \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                ->whereIn('order_id', $deliveredOrderIds)
            : \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)->whereRaw('1=0'); // Empty query if no stage

        // Get customers visible to this vendor:
        // 1. System customers (vendor_id is NULL)
        // 2. Customers created by this vendor (vendor_id matches)
        $vendorCustomersQuery = Customer::where(function($q) use ($vendorId) {
            $q->whereNull('vendor_id')
              ->orWhere('vendor_id', $vendorId);
        });

        // Count customers by gender
        $totalCustomers = (clone $vendorCustomersQuery)->count();
        $maleCustomers = (clone $vendorCustomersQuery)->where('gender', 'male')->count();
        $femaleCustomers = (clone $vendorCustomersQuery)->where('gender', 'female')->count();

        return [
            // Products & Stock (vendor specific)
            'total_products' => VendorProduct::where('vendor_id', $vendorId)->count(),
            'instock' => VendorProduct::where('vendor_id', $vendorId)->whereHas('variants', function($q) {
                $q->whereHas('stocks', function($sq) {
                    $sq->where('quantity', '>', 0);
                });
            })->count(),
            'out_of_stock' => VendorProduct::where('vendor_id', $vendorId)->where(function($q) {
                $q->whereDoesntHave('variants')
                  ->orWhereDoesntHave('variants.stocks', function($sq) {
                      $sq->where('quantity', '>', 0);
                  });
            })->count(),

            // Orders (vendor specific - based on order products)
            'total_orders' => $orderProductsQuery->distinct('order_id')->count('order_id'),
            'total_sales' => (clone $deliveredOrderProducts)->selectRaw('SUM(price + COALESCE(shipping_cost, 0)) as total')->value('total') ?? 0,
            'today_sales' => (clone $deliveredOrderProducts)->whereHas('order', function($q) use ($today) {
                $q->whereDate('created_at', $today);
            })->selectRaw('SUM(price + COALESCE(shipping_cost, 0)) as total')->value('total') ?? 0,
            'today_orders' => \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                ->whereHas('order', function($q) use ($today) {
                    $q->whereDate('created_at', $today);
                })->distinct('order_id')->count('order_id'),

            // Customers (vendor specific - customers who ordered from this vendor)
            'total_customers' => $totalCustomers,
            'total_male_users' => $maleCustomers,
            'total_female_users' => $femaleCustomers,

            // Reviews (vendor specific)
            'all_products_reviews' => Review::whereHas('vendorProduct', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->count(),
            'accept_products_reviews' => Review::where('status', 'approved')->whereHas('vendorProduct', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->count(),
            'reject_products_reviews' => Review::where('status', 'rejected')->whereHas('vendorProduct', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->count(),

            // Hide admin-only stats for vendors
            'total_admins' => 0,
            'vendor_users' => 0,
            'total_vendors' => 0,
            'become_vendor_requests' => 0,
            'accepted_vendors' => 0,
            'rejected_vendors' => 0,
            'new_vendors' => 0,
            'admins_total_roles' => 0,
            'vendor_users_total_roles' => 0,
            'total_order_stages' => 0,
            'total_taxes' => 0,
            'total_messages' => 0,
            'promocodes' => 0,
            'country' => 0,
            'city' => 0,
            'region' => 0,
            'subregion' => 0,
            'total_offers' => 0,
            'total_advertisments' => 0,

            // Refunds Statistics (vendor specific)
            'refunds' => $this->getRefundStats(),
        ];
    }

    private function getSalesOverview()
    {
        $startOfYear = Carbon::now()->startOfYear();
        $newStage = OrderStage::withoutCountryFilter()->where('type', 'new')->first();
        $inProgressStage = OrderStage::withoutCountryFilter()->where('type', 'in_progress')->first();

        $totalExpenses = Expense::sum('amount');

        // Calculate net income (income - refunds)
        $totalIncome = AccountingEntry::where('type', 'income')->sum('amount');
        $totalRefunds = AccountingEntry::where('type', 'refund')->sum('amount');
        $netIncome = $totalIncome - $totalRefunds;
        
        // Calculate YTD net income
        $ytdIncome = AccountingEntry::where('type', 'income')->whereDate('created_at', '>=', $startOfYear)->sum('amount');
        $ytdRefunds = AccountingEntry::where('type', 'refund')->whereDate('created_at', '>=', $startOfYear)->sum('amount');
        $netYtdIncome = $ytdIncome - $ytdRefunds;
        
        // Get delivered stage for revenue calculation
        $deliveredStage = OrderStage::withoutCountryFilter()->where('type', 'deliver')->first();
        
        if ($this->isVendor && $this->vendorId) {
            // Vendor: Revenue Y.T.D from DELIVERED orders only
            if ($deliveredStage) {
                $revenueYtd = \Modules\Order\app\Models\OrderProduct::query()
                    ->where('order_products.vendor_id', $this->vendorId)
                    ->join('vendor_order_stages as vos', function ($join) {
                        $join->on('vos.order_id', '=', 'order_products.order_id')
                             ->on('vos.vendor_id', '=', 'order_products.vendor_id');
                    })
                    ->join('vendor_order_stage_histories as vosh', function($join) use ($deliveredStage) {
                        $join->on('vosh.vendor_order_stage_id', '=', 'vos.id')
                             ->where('vosh.new_stage_id', '=', $deliveredStage->id);
                    })
                    ->where('vos.stage_id', $deliveredStage->id)
                    ->whereDate('vosh.created_at', '>=', $startOfYear)
                    ->selectRaw('SUM(order_products.price + COALESCE(order_products.shipping_cost, 0)) as total')
                    ->value('total') ?? 0;
            } else {
                $revenueYtd = 0;
            }

            // Use vendor_order_stages table for vendor-specific stage counts
            $newOrdersCount = $newStage
                ? \Modules\Order\app\Models\VendorOrderStage::where('vendor_id', $this->vendorId)
                    ->where('stage_id', $newStage->id)
                    ->whereHas('order') // Apply country filter through order relationship
                    ->count()
                : 0;

            $inProgressOrdersCount = $inProgressStage
                ? \Modules\Order\app\Models\VendorOrderStage::where('vendor_id', $this->vendorId)
                    ->where('stage_id', $inProgressStage->id)
                    ->whereHas('order') // Apply country filter through order relationship
                    ->count()
                : 0;
        } else {
            // Admin: Revenue Y.T.D from DELIVERED orders only
            if ($deliveredStage) {
                $revenueYtd = \Modules\Order\app\Models\OrderProduct::query()
                    ->join('vendor_order_stages as vos', function ($join) {
                        $join->on('vos.order_id', '=', 'order_products.order_id')
                             ->on('vos.vendor_id', '=', 'order_products.vendor_id');
                    })
                    ->join('vendor_order_stage_histories as vosh', function($join) use ($deliveredStage) {
                        $join->on('vosh.vendor_order_stage_id', '=', 'vos.id')
                             ->where('vosh.new_stage_id', '=', $deliveredStage->id);
                    })
                    ->where('vos.stage_id', $deliveredStage->id)
                    ->whereDate('vosh.created_at', '>=', $startOfYear)
                    ->selectRaw('SUM(order_products.price + COALESCE(order_products.shipping_cost, 0)) as total')
                    ->value('total') ?? 0;
            } else {
                $revenueYtd = 0;
            }

            // Admin: count based on vendor_order_stages (per-vendor stage counts)
            $newOrdersCount = $newStage 
                ? \Modules\Order\app\Models\VendorOrderStage::where('stage_id', $newStage->id)
                    ->whereHas('order') // Apply country filter through order relationship
                    ->count() 
                : 0;

            $inProgressOrdersCount = $inProgressStage 
                ? \Modules\Order\app\Models\VendorOrderStage::where('stage_id', $inProgressStage->id)
                    ->whereHas('order') // Apply country filter through order relationship
                    ->count() 
                : 0;
        }

        return [
            'total_expenses' => $totalExpenses,
            'total_income' => $netIncome,
            'net_profit_ytd' => $netYtdIncome - $totalExpenses,
            'revenue_ytd' => $revenueYtd - $ytdRefunds,  // Subtract refunds from revenue
            'new_orders_count' => $newOrdersCount,
            'in_progress_orders_count' => $inProgressOrdersCount,
        ];
    }

    private function getOrdersOverview()
    {
        // Get all order stages dynamically
        $stages = OrderStage::withoutCountryFilter()->orderBy('id', 'asc')->get();

        $overview = [];

        foreach ($stages as $stage) {
            if ($this->isVendor && $this->vendorId) {
                // Vendor-specific: count based on vendor's specific stage in vendor_order_stages table
                // Filter through order relationship since VendorOrderStage doesn't have country_id
                $count = \Modules\Order\app\Models\VendorOrderStage::where('vendor_id', $this->vendorId)
                    ->where('stage_id', $stage->id)
                    ->whereHas('order') // This will apply the country filter from Order model's global scope
                    ->count();
            } else {
                // Admin: count all vendor stages (each vendor in an order has their own stage)
                // Filter through order relationship for country filtering
                $count = \Modules\Order\app\Models\VendorOrderStage::where('stage_id', $stage->id)
                    ->whereHas('order') // This will apply the country filter from Order model's global scope
                    ->count();
            }

            $overview[] = [
                'id' => $stage->id,
                'name' => $stage->name,
                'slug' => $stage->slug,
                'type' => $stage->type,
                'color' => $stage->color ?? '#6c757d',
                'count' => $count,
            ];
        }

        return $overview;
    }

    private function getIncomeExpenseData()
    {
        $now = Carbon::now();
        // This Month data
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Calculate monthly income and refunds
        $monthlyIncomeGross = AccountingEntry::where('type', 'income')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        $monthlyRefunds = AccountingEntry::where('type', 'refund')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        $monthlyIncome = $monthlyIncomeGross - $monthlyRefunds;

        $monthlyExpenses = Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])->sum('amount');
        
        // Calculate commission (income commission - refunded commission)
        $monthlyCommissionGross = AccountingEntry::where('type', 'income')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('commission_amount');
        $monthlyRefundedCommission = AccountingEntry::where('type', 'refund')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('commission_amount');
        $monthlyCommission = $monthlyCommissionGross - $monthlyRefundedCommission;
        
        // Net profit = Income - Commissions - Expenses
        $monthlyProfit = $monthlyIncome - $monthlyCommission - $monthlyExpenses;

        // This Year data
        $startOfYear = $now->copy()->startOfYear();
        $endOfYear = $now->copy()->endOfYear();

        // Calculate yearly income and refunds
        $yearlyIncomeGross = AccountingEntry::where('type', 'income')
            ->whereBetween('created_at', [$startOfYear, $endOfYear])
            ->sum('amount');
        $yearlyRefunds = AccountingEntry::where('type', 'refund')
            ->whereBetween('created_at', [$startOfYear, $endOfYear])
            ->sum('amount');
        $yearlyIncome = $yearlyIncomeGross - $yearlyRefunds;

        $yearlyExpenses = Expense::whereBetween('expense_date', [$startOfYear, $endOfYear])->sum('amount');
        
        // Calculate commission (income commission - refunded commission)
        $yearlyCommissionGross = AccountingEntry::where('type', 'income')
            ->whereBetween('created_at', [$startOfYear, $endOfYear])
            ->sum('commission_amount');
        $yearlyRefundedCommission = AccountingEntry::where('type', 'refund')
            ->whereBetween('created_at', [$startOfYear, $endOfYear])
            ->sum('commission_amount');
        $yearlyCommission = $yearlyCommissionGross - $yearlyRefundedCommission;
        
        // Net profit = Income - Commissions - Expenses
        $yearlyProfit = $yearlyIncome - $yearlyCommission - $yearlyExpenses;

        // Monthly breakdown for chart (current year)
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($now->year, $month, 1)->startOfMonth();
            $monthEnd = Carbon::create($now->year, $month, 1)->endOfMonth();

            $incomeGross = AccountingEntry::where('type', 'income')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');
            $refunds = AccountingEntry::where('type', 'refund')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');
            $income = $incomeGross - $refunds;

            $expenses = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])->sum('amount');
            
            $commissionGross = AccountingEntry::where('type', 'income')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('commission_amount');
            $refundedCommission = AccountingEntry::where('type', 'refund')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('commission_amount');
            $commission = $commissionGross - $refundedCommission;

            $monthlyData[] = [
                'month' => $month,
                'income' => $income,
                'expenses' => $expenses,
                'commission' => $commission,
                'refunds' => $refunds,
            ];
        }

        // Daily breakdown for current month chart
        $dailyData = [];
        $daysInMonth = $now->daysInMonth;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayDate = Carbon::create($now->year, $now->month, $day);

            $incomeGross = AccountingEntry::where('type', 'income')
                ->whereDate('created_at', $dayDate)
                ->sum('amount');
            $refunds = AccountingEntry::where('type', 'refund')
                ->whereDate('created_at', $dayDate)
                ->sum('amount');
            $income = $incomeGross - $refunds;

            $expenses = Expense::whereDate('expense_date', $dayDate)->sum('amount');
            
            $commissionGross = AccountingEntry::where('type', 'income')
                ->whereDate('created_at', $dayDate)
                ->sum('commission_amount');
            $refundedCommission = AccountingEntry::where('type', 'refund')
                ->whereDate('created_at', $dayDate)
                ->sum('commission_amount');
            $commission = $commissionGross - $refundedCommission;

            $dailyData[] = [
                'day' => $day,
                'income' => $income,
                'expenses' => $expenses,
                'commission' => $commission,
                'refunds' => $refunds,
            ];
        }

        return [
            'month' => [
                'income' => $monthlyIncome,
                'expenses' => $monthlyExpenses,
                'commission' => $monthlyCommission,
                'refunds' => $monthlyRefunds,
                'profit' => $monthlyProfit,
                'period' => $now->format('m-Y'),
                'daily_data' => $dailyData,
            ],
            'year' => [
                'income' => $yearlyIncome,
                'expenses' => $yearlyExpenses,
                'commission' => $yearlyCommission,
                'refunds' => $yearlyRefunds,
                'profit' => $yearlyProfit,
                'period' => $now->year,
                'monthly_data' => $monthlyData,
            ],
        ];
    }

    private function getSalesChartData()
    {
        $now = Carbon::now();

        if ($this->isVendor && $this->vendorId) {
            return $this->getVendorSalesChartData($now);
        }

        // Monthly data (last 12 months) - all orders
        $endDate = $now->copy();
        $startDate = $now->copy()->subYear();
        $monthlySales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_price) as total_sales')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $labels = $monthlySales->pluck('month');
        $data = $monthlySales->pluck('total_sales');

        // Hourly data (today - all 24 hours) - all orders
        $hourly = [];
        for ($i = 0; $i < 24; $i++) {
            $hourStart = $now->copy()->startOfDay()->addHours($i);
            $hourEnd = $now->copy()->startOfDay()->addHours($i + 1);
            $hourly[] = Order::whereBetween('created_at', [$hourStart, $hourEnd])->sum('total_price') ?? 0;
        }

        // Weekly data (this week) - all orders
        $weekly = [];
        $startOfWeek = $now->copy()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $dayStart = $startOfWeek->copy()->addDays($i)->startOfDay();
            $dayEnd = $startOfWeek->copy()->addDays($i)->endOfDay();
            $weekly[] = Order::whereBetween('created_at', [$dayStart, $dayEnd])->sum('total_price') ?? 0;
        }

        // Daily data (current month) - all orders
        $daily = [];
        $daysInMonth = $now->daysInMonth;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayDate = Carbon::create($now->year, $now->month, $day);
            $daily[] = Order::whereDate('created_at', $dayDate)->sum('total_price') ?? 0;
        }

        // Monthly breakdown for current year - all orders
        $monthly = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($now->year, $month, 1)->startOfMonth();
            $monthEnd = Carbon::create($now->year, $month, 1)->endOfMonth();
            $monthly[] = Order::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_price') ?? 0;
        }

        // Yearly data (last 5 years) - all orders
        $yearlyLabels = [];
        $yearlyData = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = $now->year - $i;
            $yearlyLabels[] = $year;
            $yearStart = Carbon::create($year, 1, 1)->startOfYear();
            $yearEnd = Carbon::create($year, 12, 31)->endOfYear();
            $yearlyData[] = Order::whereBetween('created_at', [$yearStart, $yearEnd])->sum('total_price') ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'hourly' => $hourly,
            'weekly' => $weekly,
            'daily' => $daily,
            'monthly' => $monthly,
            'yearly_labels' => $yearlyLabels,
            'yearly_data' => $yearlyData,
        ];
    }

    private function getEarningsChartData()
    {
        $now = Carbon::now();
        $deliveredStage = OrderStage::withoutCountryFilter()->where('type', 'deliver')->first();

        if ($this->isVendor && $this->vendorId) {
            return $this->getVendorEarningsChartData($now, $deliveredStage);
        }

        // If no delivered stage found, return empty data
        if (!$deliveredStage) {
            return [
                'labels' => [],
                'data' => [],
                'hourly' => [0,0,0,0,0,0,0,0],
                'weekly' => [0,0,0,0,0,0,0],
                'daily' => [],
                'monthly' => [0,0,0,0,0,0,0,0,0,0,0,0],
                'yearly_labels' => [],
                'yearly_data' => [],
            ];
        }

        $deliveredStageId = $deliveredStage->id;

        // Monthly data (last 12 months) - sum only delivered vendor products
        // Use the date when the order reached deliver stage from vendor_order_stage_history
        $endDate = $now->copy();
        $startDate = $now->copy()->subYear();
        
        $monthlySales = \Modules\Order\app\Models\OrderProduct::query()
            ->join('vendor_order_stages as vos', function ($join) {
                $join->on('vos.order_id', '=', 'order_products.order_id')
                     ->on('vos.vendor_id', '=', 'order_products.vendor_id');
            })
            ->join('vendor_order_stage_histories as vosh', function($join) use ($deliveredStageId) {
                $join->on('vosh.vendor_order_stage_id', '=', 'vos.id')
                     ->where('vosh.new_stage_id', '=', $deliveredStageId);
            })
            ->where('vos.stage_id', $deliveredStageId)
            ->whereBetween('vosh.created_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(vosh.created_at, "%Y-%m") as month, SUM(order_products.price + COALESCE(order_products.shipping_cost, 0)) as total_sales')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $labels = $monthlySales->pluck('month');
        $data = $monthlySales->pluck('total_sales');

        // Helper function to calculate delivered earnings for a time period
        // Uses the date when order reached deliver stage from history
        // Includes price + shipping_cost
        $calcDeliveredEarnings = function($startTime, $endTime, $dateField = 'whereBetween') use ($deliveredStageId) {
            $query = \Modules\Order\app\Models\OrderProduct::query()
                ->join('vendor_order_stages as vos', function ($join) {
                    $join->on('vos.order_id', '=', 'order_products.order_id')
                         ->on('vos.vendor_id', '=', 'order_products.vendor_id');
                })
                ->join('vendor_order_stage_histories as vosh', function($join) use ($deliveredStageId) {
                    $join->on('vosh.vendor_order_stage_id', '=', 'vos.id')
                         ->where('vosh.new_stage_id', '=', $deliveredStageId);
                })
                ->where('vos.stage_id', $deliveredStageId);
            
            if ($dateField === 'whereDate') {
                $query->whereDate('vosh.created_at', $startTime);
            } else {
                $query->whereBetween('vosh.created_at', [$startTime, $endTime]);
            }
            
            return $query->selectRaw('SUM(order_products.price + COALESCE(order_products.shipping_cost, 0)) as total')->value('total') ?? 0;
        };

        // Hourly data (today - all 24 hours)
        $hourly = [];
        for ($i = 0; $i < 24; $i++) {
            $hourStart = $now->copy()->startOfDay()->addHours($i);
            $hourEnd = $now->copy()->startOfDay()->addHours($i + 1);
            $hourly[] = $calcDeliveredEarnings($hourStart, $hourEnd);
        }

        // Weekly data (this week)
        $weekly = [];
        $startOfWeek = $now->copy()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $dayStart = $startOfWeek->copy()->addDays($i)->startOfDay();
            $dayEnd = $startOfWeek->copy()->addDays($i)->endOfDay();
            $weekly[] = $calcDeliveredEarnings($dayStart, $dayEnd);
        }

        // Daily data (current month)
        $daily = [];
        $daysInMonth = $now->daysInMonth;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayDate = Carbon::create($now->year, $now->month, $day);
            $daily[] = $calcDeliveredEarnings($dayDate, null, 'whereDate');
        }

        // Monthly breakdown for current year
        $monthly = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($now->year, $month, 1)->startOfMonth();
            $monthEnd = Carbon::create($now->year, $month, 1)->endOfMonth();
            $monthly[] = $calcDeliveredEarnings($monthStart, $monthEnd);
        }

        // Yearly data (last 5 years)
        $yearlyLabels = [];
        $yearlyData = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = $now->year - $i;
            $yearlyLabels[] = $year;
            $yearStart = Carbon::create($year, 1, 1)->startOfYear();
            $yearEnd = Carbon::create($year, 12, 31)->endOfYear();
            $yearlyData[] = $calcDeliveredEarnings($yearStart, $yearEnd);
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'hourly' => $hourly,
            'weekly' => $weekly,
            'daily' => $daily,
            'monthly' => $monthly,
            'yearly_labels' => $yearlyLabels,
            'yearly_data' => $yearlyData,
        ];
    }

    private function getVendorEarningsChartData($now, $deliveredStage)
    {
        $vendorId = $this->vendorId;
        
        if (!$deliveredStage) {
            return [
                'labels' => [],
                'data' => [],
                'hourly' => array_fill(0, 24, 0),
                'weekly' => array_fill(0, 7, 0),
                'daily' => [],
                'monthly' => array_fill(0, 12, 0),
                'yearly_labels' => [],
                'yearly_data' => [],
            ];
        }
        
        $deliveredStageId = $deliveredStage->id;

        // Monthly data (last 12 months) - use delivery date from history
        $endDate = $now->copy();
        $startDate = $now->copy()->subYear();

        $monthlySales = \Modules\Order\app\Models\OrderProduct::query()
            ->where('order_products.vendor_id', $vendorId)
            ->join('vendor_order_stages as vos', function ($join) use ($vendorId) {
                $join->on('vos.order_id', '=', 'order_products.order_id')
                     ->where('vos.vendor_id', '=', $vendorId);
            })
            ->join('vendor_order_stage_histories as vosh', function($join) use ($deliveredStageId) {
                $join->on('vosh.vendor_order_stage_id', '=', 'vos.id')
                     ->where('vosh.new_stage_id', '=', $deliveredStageId);
            })
            ->where('vos.stage_id', $deliveredStageId)
            ->whereBetween('vosh.created_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(vosh.created_at, "%Y-%m") as month, SUM(order_products.price + COALESCE(order_products.shipping_cost, 0)) as total_sales')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $labels = $monthlySales->pluck('month');
        $data = $monthlySales->pluck('total_sales');

        // Helper function for vendor earnings (includes shipping)
        $calcVendorEarnings = function($startTime, $endTime, $dateField = 'whereBetween') use ($vendorId, $deliveredStageId) {
            $query = \Modules\Order\app\Models\OrderProduct::query()
                ->where('order_products.vendor_id', $vendorId)
                ->join('vendor_order_stages as vos', function ($join) use ($vendorId) {
                    $join->on('vos.order_id', '=', 'order_products.order_id')
                         ->where('vos.vendor_id', '=', $vendorId);
                })
                ->join('vendor_order_stage_histories as vosh', function($join) use ($deliveredStageId) {
                    $join->on('vosh.vendor_order_stage_id', '=', 'vos.id')
                         ->where('vosh.new_stage_id', '=', $deliveredStageId);
                })
                ->where('vos.stage_id', $deliveredStageId);
            
            if ($dateField === 'whereDate') {
                $query->whereDate('vosh.created_at', $startTime);
            } else {
                $query->whereBetween('vosh.created_at', [$startTime, $endTime]);
            }
            
            return $query->selectRaw('SUM(order_products.price + COALESCE(order_products.shipping_cost, 0)) as total')->value('total') ?? 0;
        };

        // Hourly data (today - all 24 hours)
        $hourly = [];
        for ($i = 0; $i < 24; $i++) {
            $hourStart = $now->copy()->startOfDay()->addHours($i);
            $hourEnd = $now->copy()->startOfDay()->addHours($i + 1);
            $hourly[] = $calcVendorEarnings($hourStart, $hourEnd);
        }

        // Weekly data (this week)
        $weekly = [];
        $startOfWeek = $now->copy()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $dayStart = $startOfWeek->copy()->addDays($i)->startOfDay();
            $dayEnd = $startOfWeek->copy()->addDays($i)->endOfDay();
            $weekly[] = $calcVendorEarnings($dayStart, $dayEnd);
        }

        // Daily data (current month)
        $daily = [];
        $daysInMonth = $now->daysInMonth;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayDate = Carbon::create($now->year, $now->month, $day);
            $daily[] = $calcVendorEarnings($dayDate, null, 'whereDate');
        }

        // Monthly breakdown for current year
        $monthly = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($now->year, $month, 1)->startOfMonth();
            $monthEnd = Carbon::create($now->year, $month, 1)->endOfMonth();
            $monthly[] = $calcVendorEarnings($monthStart, $monthEnd);
        }

        // Yearly data (last 5 years)
        $yearlyLabels = [];
        $yearlyData = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = $now->year - $i;
            $yearlyLabels[] = $year;
            $yearStart = Carbon::create($year, 1, 1)->startOfYear();
            $yearEnd = Carbon::create($year, 12, 31)->endOfYear();
            $yearlyData[] = $calcVendorEarnings($yearStart, $yearEnd);
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'hourly' => $hourly,
            'weekly' => $weekly,
            'daily' => $daily,
            'monthly' => $monthly,
            'yearly_labels' => $yearlyLabels,
            'yearly_data' => $yearlyData,
        ];
    }

    private function getVendorSalesChartData($now)
    {
        $vendorId = $this->vendorId;

        // Monthly data (last 12 months) - ALL orders for this vendor
        $endDate = $now->copy();
        $startDate = $now->copy()->subYear();

        $monthlySales = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
            ->whereHas('order', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->join('orders', 'order_products.order_id', '=', 'orders.id')
            ->selectRaw('DATE_FORMAT(orders.created_at, "%Y-%m") as month, SUM(order_products.price) as total_sales')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $labels = $monthlySales->pluck('month');
        $data = $monthlySales->pluck('total_sales');

        // Hourly data (today - all 24 hours) - ALL orders for this vendor
        $hourly = [];
        for ($i = 0; $i < 24; $i++) {
            $hourStart = $now->copy()->startOfDay()->addHours($i);
            $hourEnd = $now->copy()->startOfDay()->addHours($i + 1);
            $hourly[] = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                ->whereHas('order', function($q) use ($hourStart, $hourEnd) {
                    $q->whereBetween('created_at', [$hourStart, $hourEnd]);
                })->sum('price') ?? 0;
        }

        // Weekly data (this week) - ALL orders for this vendor
        $weekly = [];
        $startOfWeek = $now->copy()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $dayStart = $startOfWeek->copy()->addDays($i)->startOfDay();
            $dayEnd = $startOfWeek->copy()->addDays($i)->endOfDay();
            $weekly[] = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                ->whereHas('order', function($q) use ($dayStart, $dayEnd) {
                    $q->whereBetween('created_at', [$dayStart, $dayEnd]);
                })->sum('price') ?? 0;
        }

        // Daily data (current month) - ALL orders for this vendor
        $daily = [];
        $daysInMonth = $now->daysInMonth;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayDate = Carbon::create($now->year, $now->month, $day);
            $daily[] = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                ->whereHas('order', function($q) use ($dayDate) {
                    $q->whereDate('created_at', $dayDate);
                })->sum('price') ?? 0;
        }

        // Monthly breakdown for current year - ALL orders for this vendor
        $monthly = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($now->year, $month, 1)->startOfMonth();
            $monthEnd = Carbon::create($now->year, $month, 1)->endOfMonth();
            $monthly[] = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                ->whereHas('order', function($q) use ($monthStart, $monthEnd) {
                    $q->whereBetween('created_at', [$monthStart, $monthEnd]);
                })->sum('price') ?? 0;
        }

        // Yearly data (last 5 years) - ALL orders for this vendor
        $yearlyLabels = [];
        $yearlyData = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = $now->year - $i;
            $yearlyLabels[] = $year;
            $yearStart = Carbon::create($year, 1, 1)->startOfYear();
            $yearEnd = Carbon::create($year, 12, 31)->endOfYear();
            $yearlyData[] = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $vendorId)
                ->whereHas('order', function($q) use ($yearStart, $yearEnd) {
                    $q->whereBetween('created_at', [$yearStart, $yearEnd]);
                })->sum('price') ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'hourly' => $hourly,
            'weekly' => $weekly,
            'daily' => $daily,
            'monthly' => $monthly,
            'yearly_labels' => $yearlyLabels,
            'yearly_data' => $yearlyData,
        ];
    }

    private function getLatestOrders($limit = 5)
    {
        if ($this->isVendor && $this->vendorId) {
            // Get orders that contain products from this vendor
            $orderIds = \Modules\Order\app\Models\OrderProduct::where('vendor_id', $this->vendorId)
                ->distinct()
                ->pluck('order_id');

            $orders = Order::with(['customer', 'stage', 'products.vendorProduct.product'])
                ->whereIn('id', $orderIds)
                ->latest()
                ->take($limit)
                ->get();

            // Calculate vendor's product total for each order
            $vendorId = $this->vendorId;
            foreach ($orders as $order) {
                $vendorProductTotal = $order->products
                    ->where('vendor_id', $vendorId)
                    ->sum(function($product) {
                        return $product->price * $product->quantity;
                    });
                $order->vendor_product_total = $vendorProductTotal;
            }

            return $orders;
        }

        return Order::with(['customer', 'stage', 'products.vendorProduct.product'])
            ->latest()
            ->take($limit)
            ->get();
    }

    private function getTopSellingProducts($limit = 5)
    {
        // Get products with most sold quantity
        $query = \Modules\Order\app\Models\OrderProduct::select('vendor_product_id', 'vendor_id')
            ->selectRaw('SUM(quantity) as total_sold')
            ->selectRaw('SUM(price) as total_revenue')
            ->whereNotNull('vendor_product_id')
            ->whereHas('order'); // Apply country filter through order relationship

        // Filter by vendor if logged in as vendor
        if ($this->isVendor && $this->vendorId) {
            $query->where('vendor_id', $this->vendorId);
        }

        $topProducts = $query->groupBy('vendor_product_id', 'vendor_id')
            ->orderByDesc('total_sold')
            ->take($limit)
            ->get();

        // Load relationships after grouping
        return $topProducts->map(function($item) {
            $item->vendorProduct = \Modules\CatalogManagement\app\Models\VendorProduct::with(['product.translations', 'product.mainImage'])->find($item->vendor_product_id);
            $item->vendorData = \Modules\Vendor\app\Models\Vendor::with(['translations', 'logo'])->find($item->vendor_id);
            return $item;
        });
    }

    private function getTopVendors($limit = 5)
    {
        return Vendor::with(['translations', 'logo'])
            ->withSum('total_orders', 'price')
            ->withCount('total_orders')
            ->orderByDesc('total_orders_sum_price')
            ->take($limit)
            ->get();
    }

    private function getBestCustomers($limit = 5)
    {
        // Get current country_id from session
        $countryCode = session('country_code', 'eg');
        $countryId = Country::where('code', $countryCode)->value('id');
        
        if ($this->isVendor && $this->vendorId) {
            // For vendors: Get all customers (registered + external) who bought from this vendor
            // Registered customers
            $registeredCustomers = \DB::table('orders')
                ->select(
                    'customers.id',
                    'customers.first_name',
                    'customers.last_name',
                    'customers.email',
                    'customers.image',
                    'customers.created_at',
                    \DB::raw("'registered' as customer_type")
                )
                ->selectRaw('COUNT(DISTINCT orders.id) as orders_count')
                ->selectRaw('SUM(order_products.price) as orders_sum_total_price')
                ->join('customers', 'orders.customer_id', '=', 'customers.id')
                ->join('order_products', 'orders.id', '=', 'order_products.order_id')
                ->where('order_products.vendor_id', $this->vendorId)
                ->where('orders.country_id', $countryId)
                ->whereNotNull('orders.customer_id')
                ->groupBy('customers.id', 'customers.first_name', 'customers.last_name', 'customers.email', 'customers.image', 'customers.created_at');

            // External customers (grouped by name + email)
            $externalCustomers = \DB::table('orders')
                ->select(
                    \DB::raw('NULL as id'),
                    \DB::raw("SUBSTRING_INDEX(orders.customer_name, ' ', 1) as first_name"),
                    \DB::raw("SUBSTRING_INDEX(orders.customer_name, ' ', -1) as last_name"),
                    'orders.customer_email as email',
                    \DB::raw('NULL as image'),
                    \DB::raw('MIN(orders.created_at) as created_at'),
                    \DB::raw("'external' as customer_type")
                )
                ->selectRaw('COUNT(DISTINCT orders.id) as orders_count')
                ->selectRaw('SUM(order_products.price) as orders_sum_total_price')
                ->join('order_products', 'orders.id', '=', 'order_products.order_id')
                ->where('order_products.vendor_id', $this->vendorId)
                ->where('orders.country_id', $countryId)
                ->whereNull('orders.customer_id')
                ->whereNotNull('orders.customer_name')
                ->groupBy('orders.customer_name', 'orders.customer_email');

            // Union and order by total
            $customers = $registeredCustomers
                ->union($externalCustomers)
                ->orderByDesc('orders_sum_total_price')
                ->limit($limit)
                ->get();

            // Transform to collection with full_name attribute
            return $customers->map(function ($customer) {
                $customer->full_name = trim($customer->first_name . ' ' . $customer->last_name);
                return $customer;
            });
        }
        // For admin: Get all customers (registered + external) with highest total order amounts
        // Registered customers
        $registeredCustomers = \DB::table('orders')
            ->select(
                'customers.id',
                'customers.first_name',
                'customers.last_name',
                'customers.email',
                'customers.image',
                'customers.created_at',
                \DB::raw("'registered' as customer_type")
            )
            ->selectRaw('COUNT(orders.id) as orders_count')
            ->selectRaw('SUM(orders.total_price) as orders_sum_total_price')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('orders.country_id', $countryId)
            ->whereNotNull('orders.customer_id')
            ->groupBy('customers.id', 'customers.first_name', 'customers.last_name', 'customers.email', 'customers.image', 'customers.created_at');

        // External customers (grouped by name + email)
        $externalCustomers = \DB::table('orders')
            ->select(
                \DB::raw('NULL as id'),
                \DB::raw("SUBSTRING_INDEX(customer_name, ' ', 1) as first_name"),
                \DB::raw("SUBSTRING_INDEX(customer_name, ' ', -1) as last_name"),
                'customer_email as email',
                \DB::raw('NULL as image'),
                \DB::raw('MIN(created_at) as created_at'),
                \DB::raw("'external' as customer_type")
            )
            ->selectRaw('COUNT(id) as orders_count')
            ->selectRaw('SUM(total_price) as orders_sum_total_price')
            ->where('country_id', $countryId)
            ->whereNull('customer_id')
            ->whereNotNull('customer_name')
            ->groupBy('customer_name', 'customer_email');

        // Union and order by total
        $customers = $registeredCustomers
            ->union($externalCustomers)
            ->orderByDesc('orders_sum_total_price')
            ->limit($limit)
            ->get();

        // Transform to collection with full_name attribute
        return $customers->map(function ($customer) {
            $customer->full_name = trim($customer->first_name . ' ' . $customer->last_name);
            return $customer;
        });
    }

    private function getRecentActivities($limit = 5)
    {
        return \App\Models\ActivityLog::with(['user', 'user.translations'])
            ->latest()
            ->take($limit)
            ->get();
    }
        /**
     * Get refund statistics
     */
    private function getRefundStats()
    {
        $now = Carbon::now();
        $refundQuery = \Modules\Refund\app\Models\RefundRequest::query();
        
        // Filter by vendor if applicable
        if ($this->isVendor && $this->vendorId) {
            $refundQuery->where('vendor_id', $this->vendorId);
        }
        
        // Hourly data (today - all 24 hours)
        $hourlyData = [];
        for ($i = 0; $i < 24; $i++) {
            $hourStart = $now->copy()->startOfDay()->addHours($i);
            $hourEnd = $now->copy()->startOfDay()->addHours($i + 1);
            
            $hourRefunds = (clone $refundQuery)
                ->where('status', 'refunded')
                ->where(function($q) use ($hourStart, $hourEnd) {
                    $q->whereBetween('refunded_at', [$hourStart, $hourEnd])
                      ->orWhere(function($q2) use ($hourStart, $hourEnd) {
                          $q2->whereNull('refunded_at')
                             ->whereBetween('created_at', [$hourStart, $hourEnd]);
                      });
                });
            
            $hourlyData[] = [
                'amount' => (clone $hourRefunds)->sum('total_refund_amount') ?? 0,
                'count' => (clone $hourRefunds)->count() ?? 0,
            ];
        }
        
        // Weekly data (this week)
        $weeklyData = [];
        $startOfWeek = $now->copy()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $dayStart = $startOfWeek->copy()->addDays($i)->startOfDay();
            $dayEnd = $startOfWeek->copy()->addDays($i)->endOfDay();
            
            $dayRefunds = (clone $refundQuery)
                ->where('status', 'refunded')
                ->where(function($q) use ($dayStart, $dayEnd) {
                    $q->whereBetween('refunded_at', [$dayStart, $dayEnd])
                      ->orWhere(function($q2) use ($dayStart, $dayEnd) {
                          $q2->whereNull('refunded_at')
                             ->whereBetween('created_at', [$dayStart, $dayEnd]);
                      });
                });
            
            $weeklyData[] = [
                'amount' => (clone $dayRefunds)->sum('total_refund_amount') ?? 0,
                'count' => (clone $dayRefunds)->count() ?? 0,
            ];
        }
        
        // This Month data
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        
        $monthlyRefunds = (clone $refundQuery)
            ->where('status', 'refunded')
            ->where(function($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('refunded_at', [$startOfMonth, $endOfMonth])
                  ->orWhere(function($q2) use ($startOfMonth, $endOfMonth) {
                      $q2->whereNull('refunded_at')
                         ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                  });
            });
        
        $monthlyCount = (clone $monthlyRefunds)->count();
        $monthlyAmount = (clone $monthlyRefunds)->sum('total_refund_amount');
        $monthlyProductsCount = \Modules\Refund\app\Models\RefundRequestItem::query()
            ->whereHas('refundRequest', function($q) use ($startOfMonth, $endOfMonth) {
                $q->where('status', 'refunded')
                  ->where(function($q2) use ($startOfMonth, $endOfMonth) {
                      $q2->whereBetween('refunded_at', [$startOfMonth, $endOfMonth])
                         ->orWhere(function($q3) use ($startOfMonth, $endOfMonth) {
                             $q3->whereNull('refunded_at')
                                ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                         });
                  });
                if ($this->isVendor && $this->vendorId) {
                    $q->where('vendor_id', $this->vendorId);
                }
            })
            ->sum('quantity');
        
        // Daily breakdown for current month chart
        $dailyData = [];
        $daysInMonth = $now->daysInMonth;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayDate = Carbon::create($now->year, $now->month, $day);
            
            $dayRefunds = (clone $refundQuery)
                ->where('status', 'refunded')
                ->where(function($q) use ($dayDate) {
                    $q->whereDate('refunded_at', $dayDate)
                      ->orWhere(function($q2) use ($dayDate) {
                          $q2->whereNull('refunded_at')
                             ->whereDate('created_at', $dayDate);
                      });
                });
            
            $dailyData[] = [
                'day' => $day,
                'amount' => (clone $dayRefunds)->sum('total_refund_amount') ?? 0,
                'count' => (clone $dayRefunds)->count() ?? 0,
            ];
        }
        
        // This Year data
        $startOfYear = $now->copy()->startOfYear();
        $endOfYear = $now->copy()->endOfYear();
        
        $yearlyRefunds = (clone $refundQuery)
            ->where('status', 'refunded')
            ->where(function($q) use ($startOfYear, $endOfYear) {
                $q->whereBetween('refunded_at', [$startOfYear, $endOfYear])
                  ->orWhere(function($q2) use ($startOfYear, $endOfYear) {
                      $q2->whereNull('refunded_at')
                         ->whereBetween('created_at', [$startOfYear, $endOfYear]);
                  });
            });
        
        $yearlyCount = (clone $yearlyRefunds)->count();
        $yearlyAmount = (clone $yearlyRefunds)->sum('total_refund_amount');
        $yearlyProductsCount = \Modules\Refund\app\Models\RefundRequestItem::query()
            ->whereHas('refundRequest', function($q) use ($startOfYear, $endOfYear) {
                $q->where('status', 'refunded')
                  ->where(function($q2) use ($startOfYear, $endOfYear) {
                      $q2->whereBetween('refunded_at', [$startOfYear, $endOfYear])
                         ->orWhere(function($q3) use ($startOfYear, $endOfYear) {
                             $q3->whereNull('refunded_at')
                                ->whereBetween('created_at', [$startOfYear, $endOfYear]);
                         });
                  });
                if ($this->isVendor && $this->vendorId) {
                    $q->where('vendor_id', $this->vendorId);
                }
            })
            ->sum('quantity');
        
        // Monthly breakdown for current year chart
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($now->year, $month, 1)->startOfMonth();
            $monthEnd = Carbon::create($now->year, $month, 1)->endOfMonth();
            
            $monthRefunds = (clone $refundQuery)
                ->where('status', 'refunded')
                ->where(function($q) use ($monthStart, $monthEnd) {
                    $q->whereBetween('refunded_at', [$monthStart, $monthEnd])
                      ->orWhere(function($q2) use ($monthStart, $monthEnd) {
                          $q2->whereNull('refunded_at')
                             ->whereBetween('created_at', [$monthStart, $monthEnd]);
                      });
                });
            
            $monthlyData[] = [
                'month' => $month,
                'amount' => (clone $monthRefunds)->sum('total_refund_amount') ?? 0,
                'count' => (clone $monthRefunds)->count() ?? 0,
            ];
        }
        
        // Yearly data (last 5 years)
        $yearlyLabels = [];
        $yearlyData = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = $now->year - $i;
            $yearlyLabels[] = $year;
            $yearStart = Carbon::create($year, 1, 1)->startOfYear();
            $yearEnd = Carbon::create($year, 12, 31)->endOfYear();
            
            $yearRefunds = (clone $refundQuery)
                ->where('status', 'refunded')
                ->where(function($q) use ($yearStart, $yearEnd) {
                    $q->whereBetween('refunded_at', [$yearStart, $yearEnd])
                      ->orWhere(function($q2) use ($yearStart, $yearEnd) {
                          $q2->whereNull('refunded_at')
                             ->whereBetween('created_at', [$yearStart, $yearEnd]);
                      });
                });
            
            $yearlyData[] = [
                'year' => $year,
                'amount' => (clone $yearRefunds)->sum('total_refund_amount') ?? 0,
                'count' => (clone $yearRefunds)->count() ?? 0,
            ];
        }
        
        return [
            'hourly_data' => $hourlyData,
            'weekly_data' => $weeklyData,
            'month' => [
                'count' => $monthlyCount,
                'amount' => round($monthlyAmount, 2),
                'products_count' => $monthlyProductsCount,
                'period' => $now->format('m-Y'),
                'daily_data' => $dailyData,
            ],
            'year' => [
                'count' => $yearlyCount,
                'amount' => round($yearlyAmount, 2),
                'products_count' => $yearlyProductsCount,
                'period' => $now->year,
                'monthly_data' => $monthlyData,
            ],
            'yearly_labels' => $yearlyLabels,
            'yearly_data' => $yearlyData,
        ];
    }

    /**
     * Get Net Sales chart data (Total Sales - Refunds)
     */
    private function getNetSalesChartData()
    {
        $now = Carbon::now();
        $deliveredStage = OrderStage::withoutCountryFilter()->where('type', 'deliver')->first();
        
        // Get refund query
        $refundQuery = \Modules\Refund\app\Models\RefundRequest::query()
            ->where('status', 'refunded');
        
        // Filter by vendor if applicable
        if ($this->isVendor && $this->vendorId) {
            $refundQuery->where('vendor_id', $this->vendorId);
        }
        
        // Helper function to calculate net sales for a time period
        $calcNetSales = function($startTime, $endTime, $dateField = 'whereBetween') use ($refundQuery, $deliveredStage) {
            // Get delivered earnings (not total sales)
            $deliveredEarnings = 0;
            if ($deliveredStage) {
                $deliveredStageId = $deliveredStage->id;
                
                if ($this->isVendor && $this->vendorId) {
                    $query = \Modules\Order\app\Models\OrderProduct::query()
                        ->where('order_products.vendor_id', $this->vendorId)
                        ->join('vendor_order_stages as vos', function ($join) {
                            $join->on('vos.order_id', '=', 'order_products.order_id')
                                 ->on('vos.vendor_id', '=', 'order_products.vendor_id');
                        })
                        ->join('vendor_order_stage_histories as vosh', function($join) use ($deliveredStageId) {
                            $join->on('vosh.vendor_order_stage_id', '=', 'vos.id')
                                 ->where('vosh.new_stage_id', '=', $deliveredStageId);
                        })
                        ->where('vos.stage_id', $deliveredStageId);
                } else {
                    $query = \Modules\Order\app\Models\OrderProduct::query()
                        ->join('vendor_order_stages as vos', function ($join) {
                            $join->on('vos.order_id', '=', 'order_products.order_id')
                                 ->on('vos.vendor_id', '=', 'order_products.vendor_id');
                        })
                        ->join('vendor_order_stage_histories as vosh', function($join) use ($deliveredStageId) {
                            $join->on('vosh.vendor_order_stage_id', '=', 'vos.id')
                                 ->where('vosh.new_stage_id', '=', $deliveredStageId);
                        })
                        ->where('vos.stage_id', $deliveredStageId);
                }
                
                if ($dateField === 'whereDate') {
                    $deliveredEarnings = $query->whereDate('vosh.created_at', $startTime)
                        ->selectRaw('SUM(order_products.price + COALESCE(order_products.shipping_cost, 0)) as total')
                        ->value('total') ?? 0;
                } else {
                    $deliveredEarnings = $query->whereBetween('vosh.created_at', [$startTime, $endTime])
                        ->selectRaw('SUM(order_products.price + COALESCE(order_products.shipping_cost, 0)) as total')
                        ->value('total') ?? 0;
                }
            }
            
            // Get refunds (use refunded_at if available, otherwise fallback to created_at)
            if ($dateField === 'whereDate') {
                $refunds = (clone $refundQuery)
                    ->where(function($q) use ($startTime) {
                        $q->whereDate('refunded_at', $startTime)
                          ->orWhere(function($q2) use ($startTime) {
                              $q2->whereNull('refunded_at')
                                 ->whereDate('created_at', $startTime);
                          });
                    })
                    ->sum('total_refund_amount');
            } else {
                $refunds = (clone $refundQuery)
                    ->where(function($q) use ($startTime, $endTime) {
                        $q->whereBetween('refunded_at', [$startTime, $endTime])
                          ->orWhere(function($q2) use ($startTime, $endTime) {
                              $q2->whereNull('refunded_at')
                                 ->whereBetween('created_at', [$startTime, $endTime]);
                          });
                    })
                    ->sum('total_refund_amount');
            }
            
            return [
                'earnings' => $deliveredEarnings ?? 0,  // Changed from total_sales to earnings
                'refunds' => $refunds ?? 0,
                'net_earnings' => ($deliveredEarnings ?? 0) - ($refunds ?? 0),  // Changed from net_sales to net_earnings
            ];
        };
        
        // Hourly data (today - all 24 hours)
        $hourly = [];
        for ($i = 0; $i < 24; $i++) {
            $hourStart = $now->copy()->startOfDay()->addHours($i);
            $hourEnd = $now->copy()->startOfDay()->addHours($i + 1);
            $hourly[] = $calcNetSales($hourStart, $hourEnd);
        }
        
        // Weekly data (this week)
        $weekly = [];
        $startOfWeek = $now->copy()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $dayStart = $startOfWeek->copy()->addDays($i)->startOfDay();
            $dayEnd = $startOfWeek->copy()->addDays($i)->endOfDay();
            $weekly[] = $calcNetSales($dayStart, $dayEnd);
        }
        
        // Daily data (current month)
        $daily = [];
        $daysInMonth = $now->daysInMonth;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayDate = Carbon::create($now->year, $now->month, $day);
            $daily[] = $calcNetSales($dayDate, null, 'whereDate');
        }
        
        // Monthly breakdown for current year
        $monthly = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::create($now->year, $month, 1)->startOfMonth();
            $monthEnd = Carbon::create($now->year, $month, 1)->endOfMonth();
            $monthly[] = $calcNetSales($monthStart, $monthEnd);
        }
        
        // Yearly data (last 5 years)
        $yearlyLabels = [];
        $yearlyData = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = $now->year - $i;
            $yearlyLabels[] = $year;
            $yearStart = Carbon::create($year, 1, 1)->startOfYear();
            $yearEnd = Carbon::create($year, 12, 31)->endOfYear();
            $yearlyData[] = $calcNetSales($yearStart, $yearEnd);
        }
        
        return [
            'hourly' => $hourly,
            'weekly' => $weekly,
            'daily' => $daily,
            'monthly' => $monthly,
            'yearly_labels' => $yearlyLabels,
            'yearly_data' => $yearlyData,
        ];
    }
}


