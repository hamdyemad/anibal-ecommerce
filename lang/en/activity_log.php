<?php

return [
    // Actions
    'actions' => [
        'login' => 'Login',
        'logout' => 'Logout',
        'login_failed' => 'Login Failed',
        'created' => 'Created',
        'updated' => 'Updated',
        'deleted' => 'Deleted',
        'restored' => 'Restored',
        'force_deleted' => 'Force Deleted',
        'password_reset_requested' => 'Password Reset Requested',
        'password_reset_success' => 'Password Reset Success',
        'password_reset_failed' => 'Password Reset Failed',
        'api_read' => 'API Read',
        'api_create' => 'API Create',
        'api_update' => 'API Update',
        'api_delete' => 'API Delete',
        'api_request' => 'API Request',
    ],

    // Descriptions
    'api_request' => ':method :endpoint (Status: :status)',
    'created_model' => 'Created :model: :identifier',
    'updated_model' => 'Updated :model: :identifier',
    'deleted_model' => 'Deleted :model: :identifier',
    'restored_model' => 'Restored :model: :identifier',
    'force_deleted_model' => 'Permanently deleted :model: :identifier',
    
    'login_success' => 'User logged in successfully',
    'logout_success' => 'User logged out',
    'login_failed_inactive' => 'Failed login attempt - Account inactive',
    'login_failed_blocked' => 'Failed login attempt - Account blocked',
    'login_failed_credentials' => 'Failed login attempt - Invalid credentials',
    
    'password_reset_sent' => 'Password reset code sent to email',
    'password_reset_email_failed' => 'Failed to send password reset email',
    'password_reset_invalid_code' => 'Failed password reset - Invalid reset code',
    'password_reset_expired_code' => 'Failed password reset - Expired reset code',
    'password_reset_completed' => 'Password reset successfully',

    // Model names
    'models' => [
        // Core Models
        'User' => 'User',
        'Role' => 'Role',
        'Permission' => 'Permission',
        'Language' => 'Language',
        'Translation' => 'Translation',
        'Attachment' => 'Attachment',
        'ActivityLog' => 'Activity Log',
        
        // Area Settings
        'Country' => 'Country',
        'City' => 'City',
        'Region' => 'Region',
        'SubRegion' => 'Sub Region',
        'Currency' => 'Currency',
        
        // Catalog Management
        'Department' => 'Department',
        'Category' => 'Category',
        'SubCategory' => 'Sub Category',
        'Product' => 'Product',
        'ProductVariant' => 'Product Variant',
        'Brand' => 'Brand',
        'Tag' => 'Tag',
        'Attribute' => 'Attribute',
        'AttributeValue' => 'Attribute Value',
        'VariantConfigurationKey' => 'Variant Configuration Key',
        'VariantsConfiguration' => 'Variants Configuration',
        'VariantStock' => 'Variant Stock',
        
        // Vendor
        'Vendor' => 'Vendor',
        'VendorProduct' => 'Vendor Product',
        'VendorProductVariant' => 'Vendor Product Variant',
        'VendorProductVariantStock' => 'Vendor Product Variant Stock',
        'VendorProductTax' => 'Vendor Product Tax',
        'VendorBalance' => 'Vendor Balance',
        'VendorFcmToken' => 'Vendor FCM Token',
        'VendorRequest' => 'Vendor Request',
        
        // Customer
        'Customer' => 'Customer',
        'CustomerAddress' => 'Customer Address',
        'CustomerAccessToken' => 'Customer Access Token',
        'CustomerFcmToken' => 'Customer FCM Token',
        'CustomerOtp' => 'Customer OTP',
        'CustomerPasswordResetToken' => 'Customer Password Reset Token',
        
        // Order
        'Order' => 'Order',
        'OrderProduct' => 'Order Product',
        'OrderProductTax' => 'Order Product Tax',
        'OrderStage' => 'Order Stage',
        'OrderFulfillment' => 'Order Fulfillment',
        'OrderExtraFeeDiscount' => 'Order Extra Fee/Discount',
        'Cart' => 'Cart',
        'CartItem' => 'Cart Item',
        'Wishlist' => 'Wishlist',
        
        // Promotions
        'Occasion' => 'Occasion',
        'OccasionProduct' => 'Occasion Product',
        'Bundle' => 'Bundle',
        'BundleCategory' => 'Bundle Category',
        'BundleProduct' => 'Bundle Product',
        'Promocode' => 'Promocode',
        
        // Accounting
        'Expense' => 'Expense',
        'ExpenseItem' => 'Expense Item',
        'Income' => 'Income',
        'AccountingEntry' => 'Accounting Entry',
        'Withdraw' => 'Withdraw',
        'Transaction' => 'Transaction',
        'Payment' => 'Payment',
        
        // Shipping & Tax
        'Shipping' => 'Shipping',
        'ShippingMethod' => 'Shipping Method',
        'Tax' => 'Tax',
        'StockBooking' => 'Stock Booking',
        
        // Content Management
        'Ad' => 'Ad',
        'Slider' => 'Slider',
        'Banner' => 'Banner',
        'Blog' => 'Blog',
        'BlogCategory' => 'Blog Category',
        'BlogComment' => 'Blog Comment',
        'Faq' => 'FAQ',
        'Feature' => 'Feature',
        'FooterContent' => 'Footer Content',
        'Message' => 'Message',
        'PushNotification' => 'Push Notification',
        'Notification' => 'Notification',
        
        // Pages & Policies
        'Page' => 'Page',
        'PrivacyPolicy' => 'Privacy Policy',
        'ReturnPolicy' => 'Return Policy',
        'ServiceTerms' => 'Service Terms',
        'TermsConditions' => 'Terms & Conditions',
        
        // Settings
        'Setting' => 'Setting',
        'SiteInformation' => 'Site Information',
        'PaymentMethod' => 'Payment Method',
        
        // Points & Subscriptions
        'PointsSetting' => 'Points Setting',
        'PointsSystem' => 'Points System',
        'UserPoints' => 'User Points',
        'UserPointsTransaction' => 'User Points Transaction',
        'Subscription' => 'Subscription',
        
        // Reviews & Requests
        'Review' => 'Review',
        'RequestQuotation' => 'Request Quotation',
        
        // Other
        'Address' => 'Address',
        'Tree' => 'Tree',
        'ApiRequest' => 'API Request',
    ],
];