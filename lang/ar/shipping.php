<?php

return [
    // Page titles
    'shipping_management' => 'إدارة الشحن',
    'shippings' => 'الشحنات',
    'create_shipping' => 'إنشاء شحنة',
    'edit_shipping' => 'تعديل الشحنة',
    'view_shipping' => 'عرض الشحنة',
    'shipping_details' => 'تفاصيل الشحنة',
    'basic_information' => 'المعلومات الأساسية',
    'coverage' => 'التغطية',
    'shipping_cost' => 'تكلفة الشحن',

    // Form labels
    'name' => 'الاسم',
    'cost' => 'التكلفة',
    'status' => 'الحالة',
    'city' => 'المدينة',
    'cities' => 'المدن',
    'select_cities' => 'اختر المدن...',
    'category' => 'الفئة',
    'categories' => 'الفئات',
    'select_categories' => 'اختر الفئات...',
    'country' => 'الدولة',
    'hold_ctrl_to_select_multiple' => 'اضغط Ctrl (Cmd على Mac) لاختيار عناصر متعددة',
    'active' => 'نشط',
    'inactive' => 'غير نشط',

    // Validation messages
    'translations_required' => 'ترجمة واحدة على الأقل مطلوبة',
    'name_required' => 'الاسم مطلوب',
    'name_max_255' => 'لا يجب أن يتجاوز الاسم 255 حرفًا',
    'cost_required' => 'التكلفة مطلوبة',
    'status_required' => 'الحالة مطلوبة',
    'city_id_required' => 'المدينة مطلوبة',
    'city_ids_required' => 'مدينة واحدة على الأقل مطلوبة',
    'city_ids_min' => 'الرجاء اختيار مدينة واحدة على الأقل',
    'city_id_not_exist' => 'المدينة المحددة غير موجودة',
    'category_id_required' => 'الفئة مطلوبة',
    'category_ids_required' => 'فئة واحدة على الأقل مطلوبة',
    'category_ids_min' => 'الرجاء اختيار فئة واحدة على الأقل',
    'category_id_not_exist' => 'الفئة المحددة غير موجودة',
    'country_id_required' => 'الدولة مطلوبة',
    'department_ids_required' => 'قسم واحد على الأقل مطلوب',
    'department_ids_min' => 'الرجاء اختيار قسم واحد على الأقل',
    'department_id_not_exist' => 'القسم المحدد غير موجود',
    'sub_category_ids_required' => 'فئة فرعية واحدة على الأقل مطلوبة',
    'sub_category_ids_min' => 'الرجاء اختيار فئة فرعية واحدة على الأقل',
    'sub_category_id_not_exist' => 'الفئة الفرعية المحددة غير موجودة',

    // Success messages
    'created_successfully' => 'تم إنشاء الشحنة بنجاح',
    'updated_successfully' => 'تم تحديث الشحنة بنجاح',
    'deleted_successfully' => 'تم حذف الشحنة بنجاح',
    'status_changed_successfully' => 'تم تغيير حالة الشحنة بنجاح',

    // Error messages
    'error_creating' => 'خطأ في إنشاء الشحنة',
    'error_updating' => 'خطأ في تحديث الشحنة',
    'error_deleting' => 'خطأ في حذف الشحنة',
    'error_changing_status' => 'خطأ في تغيير حالة الشحنة',

    // Table columns
    'id' => 'المعرف',
    'title' => 'العنوان',
    'actions' => 'الإجراءات',
    'created_at' => 'تم الإنشاء في',
    'updated_at' => 'تم التحديث في',

    // Buttons
    'add_shipping' => 'إضافة شحنة',
    'edit' => 'تعديل',
    'delete' => 'حذف',
    'save' => 'حفظ',
    'cancel' => 'إلغاء',
    'back' => 'رجوع',

    // Confirmations
    'confirm_delete' => 'هل أنت متأكد من رغبتك في حذف هذه الشحنة؟',
    'delete_confirmation' => 'لا يمكن التراجع عن هذا الإجراء. سيتم حذف طريقة الشحن نهائياً.',

    // Filter labels
    'created_from' => 'تم الإنشاء من',
    'created_until' => 'تم الإنشاء حتى',

    // Shipping calculation
    'calculation_success' => 'تم حساب تكلفة الشحن بنجاح',
    'address_not_found' => 'عنوان العميل غير موجود',
    'no_shipping_available' => 'لا توجد خيارات شحن متاحة للعنوان المحدد',
    'address_id_required' => 'معرّف عنوان العميل مطلوب',
    'customer_id_required' => 'معرّف العميل مطلوب',
    'customer_id_not_found' => 'العميل غير موجود',
    'cart_items_required' => 'عناصر السلة مطلوبة',
    'cart_items_min' => 'عنصر واحد على الأقل في السلة مطلوب',
    'category_id_not_found' => 'الفئة غير موجودة',
    'department_id_not_found' => 'القسم غير موجود',
    'sub_category_id_not_found' => 'الفئة الفرعية غير موجودة',
    'product_id_required' => 'معرّف المنتج مطلوب',
    'quantity_required' => 'الكمية مطلوبة',
    'quantity_min' => 'يجب أن تكون الكمية 1 على الأقل',
    'city_not_found' => 'المدينة غير موجودة',
    'address_or_city_required' => 'عنوان العميل أو المدينة مطلوب',

    // Shipping Settings
    'shipping_settings' => 'إعدادات الشحن',
    'allow_departments' => 'السماح بالأقسام',
    'allow_departments_desc' => 'تفعيل الشحن حسب الأقسام',
    'allow_categories' => 'السماح بالفئات',
    'allow_categories_desc' => 'تفعيل الشحن حسب الفئات',
    'allow_sub_categories' => 'السماح بالفئات الفرعية',
    'allow_sub_categories_desc' => 'تفعيل الشحن حسب الفئات الفرعية',
    'settings_saved_successfully' => 'تم حفظ إعدادات الشحن بنجاح',
    'error_saving_settings' => 'خطأ في حفظ إعدادات الشحن',
    'confirm_setting_change' => 'تأكيد تغيير الإعداد',
    'setting_change_warning' => 'تحذير: تغيير هذا الإعداد سيؤدي إلى حذف جميع طرق الشحن الحالية نهائياً. لا يمكن التراجع عن هذا الإجراء.',
    'confirm_change' => 'تأكيد التغيير',
    'departments' => 'الأقسام',
    'select_departments' => 'اختر الأقسام...',
    'sub_categories' => 'الفئات الفرعية',
    'select_sub_categories' => 'اختر الفئات الفرعية...',
];
