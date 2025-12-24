<?php

return [
    // Page titles
    'push_notifications' => 'الإشعارات',
    'all_notifications' => 'جميع الإشعارات',
    'send_notification' => 'إرسال إشعار',
    'notification_details' => 'تفاصيل الإشعار',

    // Types
    'notification_type' => 'نوع الإشعار',
    'type_all' => 'جميع العملاء',
    'type_specific' => 'عملاء محددين',

    // Status
    'status_pending' => 'قيد الانتظار',
    'status_sent' => 'تم الإرسال',
    'status_failed' => 'فشل',

    // Fields
    'title' => 'العنوان',
    'title_en' => 'العنوان (إنجليزي)',
    'title_ar' => 'العنوان (عربي)',
    'description' => 'الوصف',
    'description_en' => 'الوصف (إنجليزي)',
    'description_ar' => 'الوصف (عربي)',
    'image' => 'الصورة',
    'upload_image' => 'انقر لرفع صورة',
    'image_size' => 'الحجم الموصى به: 800x400',
    'select_customers' => 'اختر العملاء',
    'search_customers' => 'ابحث عن عملاء...',
    'created_by' => 'أنشئ بواسطة',
    'sent_at' => 'تاريخ الإرسال',
    'recipients' => 'المستلمين',

    // Stats
    'stats' => 'الإحصائيات',
    'total_sent' => 'إجمالي المرسل',
    'success' => 'نجح',
    'failed' => 'فشل',

    // Preview
    'preview' => 'معاينة',
    'preview_note' => 'هذا هو شكل الإشعار على الأجهزة المحمولة.',
    'notification_title' => 'عنوان الإشعار',
    'notification_description' => 'وصف الإشعار سيظهر هنا.',

    // Actions
    'send' => 'إرسال الإشعار',
    'search_placeholder' => 'البحث بالعنوان...',

    // Messages
    'sent_successfully' => 'تم إرسال الإشعار بنجاح',
    'send_failed' => 'فشل إرسال الإشعار',
    'deleted_successfully' => 'تم حذف الإشعار بنجاح',
    'confirm_delete' => 'هل أنت متأكد من حذف هذا الإشعار؟',

    // Validation
    'validation' => [
        'type_required' => 'يرجى اختيار نوع الإشعار',
        'type_invalid' => 'نوع الإشعار غير صالح',
        'customers_required' => 'يرجى اختيار عميل واحد على الأقل',
        'translations_required' => 'العنوان والوصف مطلوبان',
        'title_required' => 'العنوان مطلوب',
        'description_required' => 'الوصف مطلوب',
        'image_invalid' => 'يرجى رفع صورة صالحة',
        'image_max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت',
    ],
];
