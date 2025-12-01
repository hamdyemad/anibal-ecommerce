<?php

return [
    // Page titles
    'occasions_management' => 'إدارة المناسبات',
    'add_occasion' => 'إضافة مناسبة',
    'edit_occasion' => 'تعديل مناسبة',
    'view_occasion' => 'عرض مناسبة',
    'delete_occasion' => 'حذف مناسبة',

    // Fields
    'name' => 'الاسم',
    'title' => 'العنوان',
    'sub_title' => 'العنوان الفرعي',
    'vendor' => 'التاجر',
    'image' => 'الصورة',
    'start_date' => 'تاريخ البداية',
    'end_date' => 'تاريخ النهاية',
    'status' => 'الحالة',
    'activation' => 'التفعيل',
    'active' => 'نشط',
    'inactive' => 'غير نشط',
    'all_status' => 'جميع الحالات',
    'created_at' => 'تاريخ الإنشاء',
    'created_from' => 'من تاريخ',
    'created_until' => 'إلى تاريخ',
    'no_image' => 'لا توجد صورة',

    // Product Variants
    'product_variants' => 'متغيرات المنتجات',
    'search_products' => 'البحث عن المنتجات',
    'search_products_placeholder' => 'اكتب للبحث عن المنتجات...',
    'search_products_help' => 'ابحث واختر المنتجات لإضافتها إلى هذه المناسبة',
    'searching_products' => 'جاري البحث عن المنتجات',
    'no_products_found' => 'لم يتم العثور على منتجات',
    'product_name' => 'اسم المنتج',
    'variant_name' => 'اسم المتغير',
    'sku' => 'رمز المنتج',
    'original_price' => 'السعر الأصلي',
    'special_price' => 'السعر الخاص',
    'no_products_selected' => 'لم يتم اختيار منتجات بعد',
    'product_already_added' => 'هذا المنتج مضاف بالفعل',
    'selected_products' => 'المنتجات المختارة',
    'products_selected' => 'منتج مختار',

    // SEO Fields
    'seo_information' => 'معلومات SEO',
    'seo_title' => 'عنوان SEO',
    'seo_description' => 'وصف SEO',
    'seo_keywords' => 'كلمات مفتاحية SEO',

    // Messages
    'occasion_created' => 'تم إنشاء المناسبة بنجاح',
    'occasion_updated' => 'تم تحديث المناسبة بنجاح',
    'occasion_deleted' => 'تم حذف المناسبة بنجاح',
    'error_creating_occasion' => 'خطأ في إنشاء المناسبة',
    'error_updating_occasion' => 'خطأ في تحديث المناسبة',
    'error_deleting_occasion' => 'خطأ في حذف المناسبة',
    'error_loading_data' => 'خطأ في تحميل البيانات',
    'status_changed_successfully' => 'تم تغيير الحالة بنجاح',
    'error_changing_status' => 'خطأ في تغيير الحالة',

    // Validation
    'translations_required' => 'الترجمات مطلوبة',
    'image_must_be_image' => 'يجب أن يكون الملف صورة',
    'image_invalid_format' => 'صيغة الصورة غير صالحة. الصيغ المسموحة: jpeg, png, jpg, gif, webp',
    'image_max_size' => 'يجب ألا يتجاوز حجم الصورة 2 ميجابايت',
    'active_must_be_boolean' => 'يجب أن يكون حقل التفعيل منطقياً',
    'start_date_must_be_date' => 'يجب أن يكون تاريخ البداية تاريخاً صالحاً',
    'end_date_must_be_date' => 'يجب أن يكون تاريخ النهاية تاريخاً صالحاً',
    'end_date_after_start' => 'يجب أن يكون تاريخ النهاية بعد أو يساوي تاريخ البداية',

    'name_required' => 'الاسم مطلوب',
    'name_must_be_string' => 'يجب أن يكون الاسم نصاً',
    'name_max_length' => 'يجب ألا يتجاوز الاسم 255 حرفاً',

    'title_must_be_string' => 'يجب أن يكون العنوان نصاً',
    'title_max_length' => 'يجب ألا يتجاوز العنوان 255 حرفاً',

    'sub_title_must_be_string' => 'يجب أن يكون العنوان الفرعي نصاً',
    'sub_title_max_length' => 'يجب ألا يتجاوز العنوان الفرعي 255 حرفاً',

    'seo_title_must_be_string' => 'يجب أن يكون عنوان SEO نصاً',
    'seo_title_max_length' => 'يجب ألا يتجاوز عنوان SEO 255 حرفاً',

    'seo_description_must_be_string' => 'يجب أن يكون وصف SEO نصاً',
    'seo_description_max_length' => 'يجب ألا يتجاوز وصف SEO 500 حرفاً',

    'seo_keywords_must_be_string' => 'يجب أن تكون كلمات SEO المفتاحية نصاً',
    'seo_keywords_max_length' => 'يجب ألا تتجاوز كلمات SEO المفتاحية 500 حرفاً',
];
