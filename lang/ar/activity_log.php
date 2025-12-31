<?php

return [
    // Actions
    'actions' => [
        'login' => 'تسجيل الدخول',
        'logout' => 'تسجيل الخروج',
        'login_failed' => 'فشل تسجيل الدخول',
        'created' => 'إنشاء',
        'updated' => 'تحديث',
        'deleted' => 'حذف',
        'restored' => 'استعادة',
        'force_deleted' => 'حذف نهائي',
        'password_reset_requested' => 'طلب إعادة تعيين كلمة المرور',
        'password_reset_success' => 'نجح إعادة تعيين كلمة المرور',
        'password_reset_failed' => 'فشل إعادة تعيين كلمة المرور',
    ],

    // Descriptions
    'created_model' => 'تم إنشاء :model: :identifier',
    'updated_model' => 'تم تحديث :model: :identifier',
    'deleted_model' => 'تم حذف :model: :identifier',
    'restored_model' => 'تم استعادة :model: :identifier',
    'force_deleted_model' => 'تم الحذف النهائي لـ :model: :identifier',
    
    'login_success' => 'تم تسجيل الدخول بنجاح',
    'logout_success' => 'تم تسجيل الخروج',
    'login_failed_inactive' => 'فشل تسجيل الدخول - الحساب غير مفعل',
    'login_failed_blocked' => 'فشل تسجيل الدخول - الحساب محظور',
    'login_failed_credentials' => 'فشل تسجيل الدخول - بيانات غير صحيحة',
    
    'password_reset_sent' => 'تم إرسال رمز إعادة تعيين كلمة المرور إلى البريد الإلكتروني',
    'password_reset_email_failed' => 'فشل إرسال بريد إعادة تعيين كلمة المرور',
    'password_reset_invalid_code' => 'فشل إعادة تعيين كلمة المرور - رمز غير صحيح',
    'password_reset_expired_code' => 'فشل إعادة تعيين كلمة المرور - رمز منتهي الصلاحية',
    'password_reset_completed' => 'تم إعادة تعيين كلمة المرور بنجاح',

    // Model names
    'models' => [
        // Core Models
        'User' => 'مستخدم',
        'Role' => 'دور',
        'Permission' => 'صلاحية',
        'Language' => 'لغة',
        'Translation' => 'ترجمة',
        'Attachment' => 'مرفق',
        'ActivityLog' => 'سجل النشاط',
        
        // Area Settings
        'Country' => 'دولة',
        'City' => 'مدينة',
        'Region' => 'منطقة',
        'SubRegion' => 'منطقة فرعية',
        'Currency' => 'عملة',
        
        // Catalog Management
        'Department' => 'قسم',
        'Category' => 'فئة',
        'SubCategory' => 'فئة فرعية',
        'Product' => 'منتج',
        'ProductVariant' => 'متغير المنتج',
        'Brand' => 'علامة تجارية',
        'Tag' => 'وسم',
        'Attribute' => 'سمة',
        'AttributeValue' => 'قيمة السمة',
        'VariantConfigurationKey' => 'مفتاح تكوين المتغير',
        'VariantsConfiguration' => 'تكوين المتغيرات',
        'VariantStock' => 'مخزون المتغير',
        
        // Vendor
        'Vendor' => 'بائع',
        'VendorProduct' => 'منتج البائع',
        'VendorProductVariant' => 'متغير منتج البائع',
        'VendorProductVariantStock' => 'مخزون متغير منتج البائع',
        'VendorProductTax' => 'ضريبة منتج البائع',
        'VendorBalance' => 'رصيد البائع',
        'VendorFcmToken' => 'رمز FCM للبائع',
        'VendorRequest' => 'طلب البائع',
        
        // Customer
        'Customer' => 'عميل',
        'CustomerAddress' => 'عنوان العميل',
        'CustomerAccessToken' => 'رمز وصول العميل',
        'CustomerFcmToken' => 'رمز FCM للعميل',
        'CustomerOtp' => 'رمز OTP للعميل',
        'CustomerPasswordResetToken' => 'رمز إعادة تعيين كلمة مرور العميل',
        
        // Order
        'Order' => 'طلب',
        'OrderProduct' => 'منتج الطلب',
        'OrderProductTax' => 'ضريبة منتج الطلب',
        'OrderStage' => 'مرحلة الطلب',
        'OrderFulfillment' => 'تنفيذ الطلب',
        'OrderExtraFeeDiscount' => 'رسوم/خصم إضافي للطلب',
        'Cart' => 'سلة',
        'CartItem' => 'عنصر السلة',
        'Wishlist' => 'قائمة الأمنيات',
        
        // Promotions
        'Occasion' => 'مناسبة',
        'OccasionProduct' => 'منتج المناسبة',
        'Bundle' => 'حزمة',
        'BundleCategory' => 'فئة الحزمة',
        'BundleProduct' => 'منتج الحزمة',
        'Promocode' => 'كود ترويجي',
        
        // Accounting
        'Expense' => 'مصروف',
        'ExpenseItem' => 'بند المصروف',
        'Income' => 'دخل',
        'AccountingEntry' => 'قيد محاسبي',
        'Withdraw' => 'سحب',
        'Transaction' => 'معاملة',
        'Payment' => 'دفعة',
        
        // Shipping & Tax
        'Shipping' => 'شحن',
        'ShippingMethod' => 'طريقة الشحن',
        'Tax' => 'ضريبة',
        'StockBooking' => 'حجز المخزون',
        
        // Content Management
        'Ad' => 'إعلان',
        'Slider' => 'سلايدر',
        'Banner' => 'بانر',
        'Blog' => 'مدونة',
        'BlogCategory' => 'فئة المدونة',
        'BlogComment' => 'تعليق المدونة',
        'Faq' => 'الأسئلة الشائعة',
        'Feature' => 'ميزة',
        'FooterContent' => 'محتوى التذييل',
        'Message' => 'رسالة',
        'PushNotification' => 'إشعار فوري',
        'Notification' => 'إشعار',
        
        // Pages & Policies
        'Page' => 'صفحة',
        'PrivacyPolicy' => 'سياسة الخصوصية',
        'ReturnPolicy' => 'سياسة الإرجاع',
        'ServiceTerms' => 'شروط الخدمة',
        'TermsConditions' => 'الشروط والأحكام',
        
        // Settings
        'Setting' => 'إعداد',
        'SiteInformation' => 'معلومات الموقع',
        'PaymentMethod' => 'طريقة الدفع',
        
        // Points & Subscriptions
        'PointsSetting' => 'إعدادات النقاط',
        'PointsSystem' => 'نظام النقاط',
        'UserPoints' => 'نقاط المستخدم',
        'UserPointsTransaction' => 'معاملة نقاط المستخدم',
        'Subscription' => 'اشتراك',
        
        // Reviews & Requests
        'Review' => 'تقييم',
        'RequestQuotation' => 'طلب عرض سعر',
        
        // Other
        'Address' => 'عنوان',
        'Tree' => 'شجرة',
    ],
];