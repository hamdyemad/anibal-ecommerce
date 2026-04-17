# دليل التنفيذ السريع للتقارير المالية

## الملفات التي تم إنشاؤها:

### 1. تقرير الربحية (Profitability Report)

**الملفات المطلوبة:**
- Controller: `Modules/Report/app/Http/Controllers/Web/ProfitabilityReportController.php`
- Service: `Modules/Report/app/Services/ProfitabilityService.php`
- View: `Modules/Report/resources/views/profitability.blade.php`

**الإضافات المطلوبة:**
- Routes في `Modules/Report/routes/web.php`
- Translations في `Modules/Report/lang/en/report.php` و `lang/ar/report.php`
- Menu items في `lang/en/menu.php` و `lang/ar/menu.php`

## الكود الجاهز للنسخ:

### Routes (أضف في Modules/Report/routes/web.php):
```php
// Profitability Report
Route::get('reports/profitability', [ReportController::class, 'profitability'])->name('reports.profitability');
Route::get('reports/data/profitability', [ReportController::class, 'getProfitabilityData'])->name('reports.data.profitability');

// Sales Analysis Report
Route::get('reports/sales-analysis', [ReportController::class, 'salesAnalysis'])->name('reports.sales-analysis');
Route::get('reports/data/sales-analysis', [ReportController::class, 'getSalesAnalysisData'])->name('reports.data.sales-analysis');

// Product Performance Report
Route::get('reports/product-performance', [ReportController::class, 'productPerformance'])->name('reports.product-performance');
Route::get('reports/data/product-performance', [ReportController::class, 'getProductPerformanceData'])->name('reports.data.product-performance');

// Customer Analysis Report
Route::get('reports/customer-analysis', [ReportController::class, 'customerAnalysis'])->name('reports.customer-analysis');
Route::get('reports/data/customer-analysis', [ReportController::class, 'getCustomerAnalysisData'])->name('reports.data.customer-analysis');
```

### Translations (أضف في Modules/Report/lang/en/report.php):
```php
// Profitability Report
'profitability_report' => 'Profitability Report',
'profit_loss_analysis' => 'Profit & Loss Analysis',
'total_revenue' => 'Total Revenue',
'total_costs' => 'Total Costs',
'net_profit' => 'Net Profit',
'profit_margin' => 'Profit Margin',
'orders_count' => 'Orders Count',
'average_order_value' => 'Average Order Value',
'revenue_vs_costs' => 'Revenue vs Costs',
'profit_trend' => 'Profit Trend',
'period_comparison' => 'Period Comparison',
'current_period' => 'Current Period',
'previous_period' => 'Previous Period',
'growth_rate' => 'Growth Rate',

// Sales Analysis
'sales_analysis' => 'Sales Analysis',
'sales_by_channel' => 'Sales by Channel',
'sales_by_payment_method' => 'Sales by Payment Method',
'best_selling_times' => 'Best Selling Times',
'conversion_rate' => 'Conversion Rate',

// Product Performance
'product_performance' => 'Product Performance',
'top_products' => 'Top Products',
'most_profitable_products' => 'Most Profitable Products',
'low_performing_products' => 'Low Performing Products',
'products_sold_at_loss' => 'Products Sold at Loss',
'inventory_turnover' => 'Inventory Turnover',

// Customer Analysis
'customer_analysis' => 'Customer Analysis',
'top_customers' => 'Top Customers',
'customer_lifetime_value' => 'Customer Lifetime Value',
'retention_rate' => 'Retention Rate',
'new_vs_returning' => 'New vs Returning Customers',
```

### Translations (أضف في Modules/Report/lang/ar/report.php):
```php
// Profitability Report
'profitability_report' => 'تقرير الربحية',
'profit_loss_analysis' => 'تحليل الأرباح والخسائر',
'total_revenue' => 'إجمالي الإيرادات',
'total_costs' => 'إجمالي التكاليف',
'net_profit' => 'صافي الربح',
'profit_margin' => 'هامش الربح',
'orders_count' => 'عدد الطلبات',
'average_order_value' => 'متوسط قيمة الطلب',
'revenue_vs_costs' => 'الإيرادات مقابل التكاليف',
'profit_trend' => 'اتجاه الأرباح',
'period_comparison' => 'مقارنة الفترات',
'current_period' => 'الفترة الحالية',
'previous_period' => 'الفترة السابقة',
'growth_rate' => 'معدل النمو',

// Sales Analysis
'sales_analysis' => 'تحليل المبيعات',
'sales_by_channel' => 'المبيعات حسب القناة',
'sales_by_payment_method' => 'المبيعات حسب طريقة الدفع',
'best_selling_times' => 'أفضل أوقات البيع',
'conversion_rate' => 'معدل التحويل',

// Product Performance
'product_performance' => 'أداء المنتجات',
'top_products' => 'أفضل المنتجات',
'most_profitable_products' => 'المنتجات الأكثر ربحية',
'low_performing_products' => 'المنتجات الأقل أداءً',
'products_sold_at_loss' => 'المنتجات المباعة بخسارة',
'inventory_turnover' => 'معدل دوران المخزون',

// Customer Analysis
'customer_analysis' => 'تحليل العملاء',
'top_customers' => 'أفضل العملاء',
'customer_lifetime_value' => 'قيمة عمر العميل',
'retention_rate' => 'معدل الاحتفاظ',
'new_vs_returning' => 'العملاء الجدد مقابل العائدين',
```

## الخطوات التالية:

1. سأقوم بإنشاء الملفات الفعلية (Controllers, Services, Views)
2. سأضيف الروابط في القائمة الجانبية
3. سأضيف الصلاحيات المطلوبة

هل تريد أن أبدأ بإنشاء الملفات الآن؟
