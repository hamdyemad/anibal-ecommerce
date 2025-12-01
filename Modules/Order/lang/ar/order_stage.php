<?php

return [
    // General
    'order_stages_management' => 'إدارة مراحل الطلبات',
    'order_stages_list' => 'قائمة مراحل الطلبات',
    'order_stage_details' => 'تفاصيل مرحلة الطلب',
    'add_order_stage' => 'إضافة مرحلة طلب',
    'create_order_stage' => 'إنشاء مرحلة طلب',
    'edit_order_stage' => 'تعديل مرحلة الطلب',
    'update_order_stage' => 'تحديث مرحلة الطلب',
    'view_order_stage' => 'عرض مرحلة الطلب',
    'delete_order_stage' => 'حذف مرحلة الطلب',
    'search_order_stages' => 'البحث في مراحل الطلبات...',

    // Form Fields
    'name' => 'الاسم',
    'enter_order_stage_name' => 'أدخل اسم مرحلة الطلب',
    'color' => 'اللون',
    'choose_color' => 'اختر اللون',
    'sort_order' => 'ترتيب العرض',
    'activation' => 'التفعيل',
    'system_stage' => 'مرحلة نظام',
    'basic_information' => 'المعلومات الأساسية',

    // Status and Filters
    'status' => 'الحالة',
    'active' => 'نشط',
    'inactive' => 'غير نشط',
    'all_status' => 'جميع الحالات',
    'created_from' => 'تاريخ الإنشاء من',
    'created_until' => 'تاريخ الإنشاء إلى',
    'created_at' => 'تاريخ الإنشاء',

    // Messages
    'order_stage_created' => 'تم إنشاء مرحلة الطلب بنجاح',
    'order_stage_updated' => 'تم تحديث مرحلة الطلب بنجاح',
    'order_stage_deleted' => 'تم حذف مرحلة الطلب بنجاح',
    'error_creating_order_stage' => 'خطأ في إنشاء مرحلة الطلب',
    'error_updating_order_stage' => 'خطأ في تحديث مرحلة الطلب',
    'error_deleting_order_stage' => 'خطأ في حذف مرحلة الطلب',
    'delete_confirmation' => 'هل أنت متأكد من حذف مرحلة الطلب هذه؟',
    'status_changed_successfully' => 'تم تغيير الحالة بنجاح',
    'error_changing_status' => 'خطأ في تغيير الحالة',
    'status_already_set' => 'الحالة مُعيّنة بالفعل لهذه القيمة',
    'invalid_status' => 'قيمة حالة غير صحيحة',
    'cannot_delete_system_stage' => 'لا يمكن حذف مراحل النظام',

    // Validation Messages
    'translations_required' => 'الترجمات مطلوبة',
    'name_required' => 'الاسم مطلوب',
    'name_must_be_string' => 'يجب أن يكون الاسم نص',
    'name_max_length' => 'يجب ألا يزيد الاسم عن 255 حرف',
    'color_required' => 'اللون مطلوب',
    'color_invalid_format' => 'يجب أن يكون اللون بصيغة hex صحيحة (مثال: #3498db)',
    'active_must_be_boolean' => 'يجب أن يكون حقل التفعيل صحيح أو خطأ',
];
