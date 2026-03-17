# Menu Components Structure

تم تقسيم ملف الـ menu الكبير إلى components منظمة لسهولة الصيانة والتطوير.

## البنية

```
resources/views/
├── partials/
│   └── _menu.blade.php          # الملف الرئيسي (يستدعي كل الـ components)
└── components/
    └── menu/
        ├── README.md            # هذا الملف
        ├── helpers.blade.php    # Helper functions (isMenuActive, getBadgeStyle, etc.)
        ├── data.blade.php       # Data preparation (counts, queries)
        ├── scripts.blade.php    # JavaScript للـ menu
        └── sections/            # أقسام الـ menu
            ├── dashboard.blade.php
            ├── withdraw.blade.php
            ├── push-notifications.blade.php
            ├── request-quotations.blade.php
            ├── accounting.blade.php
            ├── catalog.blade.php
            ├── products.blade.php
            ├── bundles.blade.php
            ├── taxes.blade.php
            ├── occasions.blade.php
            ├── reviews.blade.php
            ├── brands.blade.php
            ├── promocodes.blade.php
            ├── points.blade.php
            ├── user-management.blade.php
            ├── vendors.blade.php
            ├── customers.blade.php
            ├── orders.blade.php
            ├── vendor-orders.blade.php
            ├── content.blade.php
            ├── messages.blade.php
            ├── reports.blade.php
            └── settings.blade.php
```

## كيفية التعديل

### إضافة menu item جديد:
1. افتح الـ section المناسب من `sections/`
2. أضف الـ menu item في المكان المناسب
3. احفظ الملف

### إضافة section جديد:
1. أنشئ ملف جديد في `sections/` مثل `new-section.blade.php`
2. أضف الكود الخاص بالـ section
3. أضف `@include('components.menu.sections.new-section')` في `_menu.blade.php`

### تعديل الـ helpers:
- افتح `helpers.blade.php` لتعديل الـ functions المساعدة

### تعديل الـ data queries:
- افتح `data.blade.php` لتعديل الـ queries والـ counts

## المميزات

✅ **منظم**: كل section في ملف منفصل
✅ **سهل الصيانة**: تعديل section واحد بدون التأثير على الباقي
✅ **قابل للتوسع**: إضافة sections جديدة بسهولة
✅ **Performance**: الـ counts متخزنة في cache (requestQuotationCounts)

## ملاحظات مهمة

- الـ `$currentRoute` و `$currentLocale` متاحين في كل الـ components
- الـ helper functions (`isMenuActive`, `getBadgeStyle`, etc.) متاحة في كل الـ sections
- الـ data variables (`$new_transactions`, `$admin_roles_count`, etc.) متاحة في كل الـ sections
