# حالة تنفيذ نظام التقارير المالية

## ✅ ما تم إنجازه:

### 1. Backend - Controllers & Services
- ✅ إضافة 4 methods في `ReportService.php`
- ✅ إضافة 8 methods في `ReportController.php` (4 للعرض + 4 للبيانات)

### 2. Routes
- ✅ إضافة 8 routes جديدة في `web.php`

### 3. Translations
- ✅ إنشاء `Modules/Report/lang/en/report.php` (كامل)
- ✅ إنشاء `Modules/Report/lang/ar/report.php` (كامل)

## ⏳ المتبقي (يحتاج تنفيذ):

### 1. Repository Methods
يجب إضافة 4 methods في `Modules/Report/app/Repositories/ReportRepository.php`:

```php
/**
 * Get profitability report
 */
public function getProfitabilityReport(ReportFilterDTO $filterDTO): array
{
    $dateFrom = $filterDTO->date_from ?? now()->startOfMonth();
    $dateTo = $filterDTO->date_to ?? now()->endOfMonth();
    
    // Get orders in date range
    $orders = \Modules\Order\app\Models\Order::whereBetween('created_at', [$dateFrom, $dateTo])
        ->whereHas('vendorOrderStages', function($q) {
            $q->whereHas('stage', function($sq) {
                $sq->where('type', 'deliver'); // Only delivered orders
            });
        })
        ->get();
    
    // Calculate metrics
    $totalRevenue = $orders->sum('total_price');
    $totalCosts = $orders->sum(function($order) {
        return $order->orderProducts->sum('commission'); // Commission as cost
    });
    $netProfit = $totalRevenue - $totalCosts;
    $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;
    $ordersCount = $orders->count();
    $averageOrderValue = $ordersCount > 0 ? $totalRevenue / $ordersCount : 0;
    
    // Get previous period for comparison
    $periodLength = $dateFrom->diffInDays($dateTo);
    $prevDateFrom = $dateFrom->copy()->subDays($periodLength);
    $prevDateTo = $dateFrom->copy()->subDay();
    
    $prevOrders = \Modules\Order\app\Models\Order::whereBetween('created_at', [$prevDateFrom, $prevDateTo])
        ->whereHas('vendorOrderStages', function($q) {
            $q->whereHas('stage', function($sq) {
                $sq->where('type', 'deliver');
            });
        })
        ->get();
    
    $prevRevenue = $prevOrders->sum('total_price');
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
                'costs' => round($prevOrders->sum(function($o) { return $o->orderProducts->sum('commission'); }), 2),
                'profit' => round($prevRevenue - $prevOrders->sum(function($o) { return $o->orderProducts->sum('commission'); }), 2),
            ],
        ],
        'monthly_trend' => $this->getMonthlyProfitTrend($dateFrom, $dateTo),
    ];
}

private function getMonthlyProfitTrend($dateFrom, $dateTo)
{
    $months = [];
    $current = $dateFrom->copy()->startOfMonth();
    
    while ($current <= $dateTo) {
        $monthOrders = \Modules\Order\app\Models\Order::whereYear('created_at', $current->year)
            ->whereMonth('created_at', $current->month)
            ->whereHas('vendorOrderStages', function($q) {
                $q->whereHas('stage', function($sq) {
                    $sq->where('type', 'deliver');
                });
            })
            ->get();
        
        $revenue = $monthOrders->sum('total_price');
        $costs = $monthOrders->sum(function($o) { return $o->orderProducts->sum('commission'); });
        
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
```

### 2. Views (4 ملفات Blade)
يجب إنشاء:
- `Modules/Report/resources/views/profitability.blade.php`
- `Modules/Report/resources/views/sales-analysis.blade.php`
- `Modules/Report/resources/views/product-performance.blade.php`
- `Modules/Report/resources/views/customer-analysis.blade.php`

### 3. إضافة في القائمة الجانبية
يجب إضافة روابط التقارير في ملف القائمة الرئيسية.

## 📝 ملاحظات:

1. **Repository Methods**: الكود أعلاه مثال لـ Profitability Report فقط
2. **Views**: تحتاج إنشاء 4 views مع Charts.js
3. **Sidebar**: تحتاج إضافة الروابط في القائمة

## 🎯 الخطوة التالية:

هل تريد أن:
1. أكمل إنشاء Repository methods للتقارير الأخرى؟
2. أم أبدأ بإنشاء View واحد كمثال كامل؟
3. أم تفضل التركيز على تقرير واحد فقط (Profitability) وإكماله بالكامل؟
