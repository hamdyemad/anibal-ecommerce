# Database Design & Schema Documentation
## Eramo Multi-Vendor E-Commerce Platform

**Version:** 1.0  
**Last Updated:** January 29, 2026  
**Database Engine:** MySQL 8.0+  
**Character Set:** utf8mb4_unicode_ci

---

## Table of Contents

1. [Overview](#overview)
2. [Core System Tables](#core-system-tables)
3. [User Management](#user-management)
4. [Vendor Management](#vendor-management)
5. [Catalog Management](#catalog-management)
6. [Order Management](#order-management)
7. [Refund Management](#refund-management)
8. [Area Settings](#area-settings)
9. [Accounting & Withdrawals](#accounting--withdrawals)
10. [System Settings](#system-settings)
11. [Relationships Overview](#relationships-overview)
12. [Indexes & Performance](#indexes--performance)

---

## Overview

The database is designed to support a multi-vendor e-commerce platform with:
- Multi-language support (Arabic & English)
- Multi-country operations
- Complex product variants with hierarchical configurations
- Vendor-specific product management
- Advanced order workflow with vendor-specific stages
- Comprehensive refund system
- Points and loyalty program
- Accounting and withdrawal management

**Total Tables:** ~80+ tables across 11 modules

---

## Core System Tables

### 1. Languages & Translations

```
languages
├── id (PK)
├── name
├── code (ar, en)
├── is_default
├── is_active
└── timestamps

translations
├── id (PK)
├── translatable_id (polymorphic)
├── translatable_type (polymorphic)
├── lang_id (FK → languages.id)
├── lang_key (name, description, etc.)
├── lang_value (translated text)
└── timestamps

Relationships:
• translations → languages (belongsTo)
• Any model → translations (morphMany)
```

### 2. Countries & Locations

```
countries
├── id (PK)
├── name
├── code
├── currency
├── currency_symbol
├── phone_code
├── is_active
└── timestamps
    │
    ├─→ cities
    │   ├── id (PK)
    │   ├── country_id (FK → countries.id)
    │   ├── name
    │   ├── is_active
    │   └── timestamps
    │       │
    │       └─→ regions
    │           ├── id (PK)
    │           ├── city_id (FK → cities.id)
    │           ├── name
    │           ├── shipping_cost
    │           ├── is_active
    │           └── timestamps
    │
    └─→ shipping_costs
        ├── id (PK)
        ├── country_id (FK → countries.id)
        ├── city_id (FK → cities.id)
        ├── region_id (FK → regions.id)
        ├── cost
        └── timestamps
```


---

## User Management

### User System Tree

```
user_types
├── id (PK)
├── name (admin, vendor, customer)
└── timestamps
    │
    └─→ users
        ├── id (PK)
        ├── user_type_id (FK → user_types.id)
        ├── name
        ├── email (unique)
        ├── password
        ├── phone
        ├── country_id (FK → countries.id)
        ├── is_active
        ├── email_verified_at
        └── timestamps
            │
            ├─→ roles (many-to-many)
            │   ├── id (PK)
            │   ├── name
            │   ├── guard_name
            │   └── timestamps
            │       │
            │       └─→ permissions (many-to-many)
            │           ├── id (PK)
            │           ├── name
            │           ├── guard_name
            │           └── timestamps
            │
            ├─→ activity_logs
            │   ├── id (PK)
            │   ├── user_id (FK → users.id)
            │   ├── customer_id (FK → customers.id)
            │   ├── action
            │   ├── model_type
            │   ├── model_id
            │   ├── old_values (JSON)
            │   ├── new_values (JSON)
            │   ├── ip_address
            │   ├── user_agent
            │   └── timestamps
            │
            └─→ admin_notifications
                ├── id (PK)
                ├── user_id (FK → users.id)
                ├── title
                ├── message
                ├── type (order, refund, withdraw, etc.)
                ├── data (JSON)
                ├── read_at
                └── timestamps
                    │
                    └─→ admin_notification_views
                        ├── id (PK)
                        ├── admin_notification_id (FK)
                        ├── user_id (FK → users.id)
                        ├── viewed_at
                        └── timestamps
```


---

## Vendor Management

### Vendor System Tree

```
vendors
├── id (PK)
├── name
├── slug (unique)
├── email
├── phone
├── country_id (FK → countries.id)
├── city_id (FK → cities.id)
├── region_id (FK → regions.id)
├── address
├── commercial_register
├── tax_number
├── bank_name
├── bank_account_number
├── iban
├── is_active
├── admin_approval
├── rejection_reason
├── logo_attachment_id (FK → attachments.id)
├── cover_attachment_id (FK → attachments.id)
└── timestamps
    │
    ├─→ vendor_users (pivot)
    │   ├── id (PK)
    │   ├── vendor_id (FK → vendors.id)
    │   ├── user_id (FK → users.id)
    │   ├── role_id (FK → roles.id)
    │   └── timestamps
    │
    ├─→ vendor_departments (many-to-many)
    │   └── vendor_department (pivot)
    │       ├── vendor_id (FK → vendors.id)
    │       ├── department_id (FK → departments.id)
    │       └── timestamps
    │
    ├─→ vendor_products
    │   ├── id (PK)
    │   ├── vendor_id (FK → vendors.id)
    │   ├── product_id (FK → products.id)
    │   ├── slug (unique)
    │   ├── sku
    │   ├── points
    │   ├── limitation (max per order)
    │   ├── status (pending, approved, rejected)
    │   ├── is_active
    │   ├── is_featured
    │   ├── is_able_to_refund
    │   ├── refund_days
    │   ├── views
    │   ├── sales
    │   ├── sort_number
    │   ├── country_id (FK → countries.id)
    │   ├── deleted_at (soft delete)
    │   └── timestamps
    │
    ├─→ vendor_refund_settings
    │   ├── id (PK)
    │   ├── vendor_id (FK → vendors.id)
    │   ├── customer_pays_return_shipping
    │   ├── refund_days
    │   └── timestamps
    │
    └─→ vendor_transactions
        ├── id (PK)
        ├── vendor_id (FK → vendors.id)
        ├── transactionable_id (polymorphic)
        ├── transactionable_type (Order, Refund, etc.)
        ├── type (credit, debit)
        ├── amount
        ├── balance_after
        ├── description
        ├── country_id (FK → countries.id)
        └── timestamps
```


---

## Catalog Management

### Product Hierarchy Tree

```
departments
├── id (PK)
├── name
├── slug (unique)
├── commission (%)
├── sort_number
├── is_active
├── image_attachment_id (FK → attachments.id)
└── timestamps
    │
    └─→ main_categories
        ├── id (PK)
        ├── department_id (FK → departments.id)
        ├── name
        ├── slug (unique)
        ├── sort_number
        ├── is_active
        ├── image_attachment_id (FK → attachments.id)
        └── timestamps
            │
            └─→ categories
                ├── id (PK)
                ├── main_category_id (FK → main_categories.id)
                ├── name
                ├── slug (unique)
                ├── sort_number
                ├── is_active
                ├── image_attachment_id (FK → attachments.id)
                └── timestamps
                    │
                    └─→ sub_categories
                        ├── id (PK)
                        ├── category_id (FK → categories.id)
                        ├── name
                        ├── slug (unique)
                        ├── sort_number
                        ├── is_active
                        ├── image_attachment_id (FK → attachments.id)
                        └── timestamps
```

### Brand System

```
brands
├── id (PK)
├── name
├── slug (unique)
├── is_active
├── logo_attachment_id (FK → attachments.id)
├── cover_attachment_id (FK → attachments.id)
└── timestamps
```


### Product & Variant System

```
products
├── id (PK)
├── department_id (FK → departments.id)
├── main_category_id (FK → main_categories.id)
├── category_id (FK → categories.id)
├── sub_category_id (FK → sub_categories.id)
├── brand_id (FK → brands.id)
├── slug (unique)
├── configuration_type (variants, bank_products)
├── country_id (FK → countries.id)
└── timestamps
    │
    ├─→ translations (polymorphic)
    │   • name, description, summary, instructions
    │   • features, extras, material, tags
    │   • meta_description, meta_keywords
    │
    ├─→ attachments (polymorphic)
    │   ├── id (PK)
    │   ├── attachable_id
    │   ├── attachable_type
    │   ├── type (main_image, additional_image, video)
    │   ├── path
    │   ├── sort_number
    │   └── timestamps
    │
    └─→ vendor_products
        ├── id (PK)
        ├── vendor_id (FK → vendors.id)
        ├── product_id (FK → products.id)
        ├── slug, sku, points, limitation
        ├── status, is_active, is_featured
        ├── is_able_to_refund, refund_days
        ├── views, sales, sort_number
        └── timestamps
            │
            ├─→ vendor_product_taxes (many-to-many)
            │   └── vendor_product_tax (pivot)
            │       ├── vendor_product_id (FK)
            │       ├── tax_id (FK → taxes.id)
            │       └── timestamps
            │
            ├─→ vendor_product_variants
            │   ├── id (PK)
            │   ├── vendor_product_id (FK)
            │   ├── variant_configuration_id (FK)
            │   ├── price
            │   ├── sku
            │   ├── barcode
            │   ├── weight
            │   ├── dimensions (JSON)
            │   └── timestamps
            │       │
            │       ├─→ variant_stocks
            │       │   ├── id (PK)
            │       │   ├── vendor_product_variant_id (FK)
            │       │   ├── region_id (FK → regions.id)
            │       │   ├── quantity
            │       │   └── timestamps
            │       │
            │       ├─→ stock_bookings
            │       │   ├── id (PK)
            │       │   ├── vendor_product_variant_id (FK)
            │       │   ├── order_id (FK → orders.id)
            │       │   ├── order_product_id (FK)
            │       │   ├── booked_quantity
            │       │   ├── status (booked, allocated, fulfilled)
            │       │   └── timestamps
            │       │
            │       └─→ order_fulfillments
            │           ├── id (PK)
            │           ├── order_id (FK → orders.id)
            │           ├── order_product_id (FK)
            │           ├── vendor_product_variant_id (FK)
            │           ├── allocated_quantity
            │           ├── status (pending, delivered)
            │           └── timestamps
            │
            ├─→ reviews (polymorphic)
            │   ├── id (PK)
            │   ├── reviewable_id
            │   ├── reviewable_type
            │   ├── customer_id (FK → customers.id)
            │   ├── star (1-5)
            │   ├── comment
            │   ├── is_approved
            │   └── timestamps
            │
            └─→ wishlists
                ├── id (PK)
                ├── customer_id (FK → customers.id)
                ├── vendor_product_id (FK)
                ├── country_id (FK → countries.id)
                └── timestamps
```


### Variant Configuration System (Hierarchical)

```
variant_configuration_keys
├── id (PK)
├── parent_key_id (FK → variant_configuration_keys.id) [self-referencing]
├── name (e.g., "Color", "Size", "Material")
├── sort_number
└── timestamps
    │
    └─→ variants_configurations
        ├── id (PK)
        ├── key_id (FK → variant_configuration_keys.id)
        ├── parent_id (FK → variants_configurations.id) [self-referencing]
        ├── name (e.g., "Red", "Large", "Cotton")
        ├── color (hex code for color variants)
        ├── sort_number
        └── timestamps
            │
            └─→ vendor_product_variants
                └── variant_configuration_id (FK)

Example Hierarchy:
• Color (key)
  ├── Red (config)
  │   └── Size (child key)
  │       ├── Small (child config)
  │       ├── Medium (child config)
  │       └── Large (child config)
  ├── Blue (config)
  │   └── Size (child key)
  │       ├── Small (child config)
  │       └── Large (child config)
  └── Green (config)
```

### Tax System

```
taxes
├── id (PK)
├── name
├── percentage
├── is_active
├── country_id (FK → countries.id)
└── timestamps
    │
    └─→ vendor_product_taxes (many-to-many)
        └── Links taxes to vendor_products
```


### Promotional Systems

```
promocodes
├── id (PK)
├── code (unique)
├── type (percentage, fixed)
├── value
├── maximum_of_use
├── start_date
├── end_date
├── is_active
├── country_id (FK → countries.id)
└── timestamps

occasions
├── id (PK)
├── name
├── slug (unique)
├── start_date
├── end_date
├── is_active
├── image_attachment_id (FK → attachments.id)
└── timestamps
    │
    └─→ occasion_products
        ├── id (PK)
        ├── occasion_id (FK → occasions.id)
        ├── vendor_product_variant_id (FK)
        ├── discount_type (percentage, fixed)
        ├── discount_value
        └── timestamps

bundles
├── id (PK)
├── bundle_category_id (FK → bundle_categories.id)
├── name
├── slug (unique)
├── discount_type (percentage, fixed)
├── discount_value
├── start_date
├── end_date
├── is_active
├── admin_approval
├── image_attachment_id (FK → attachments.id)
└── timestamps
    │
    └─→ bundle_products
        ├── id (PK)
        ├── bundle_id (FK → bundles.id)
        ├── vendor_product_variant_id (FK)
        ├── quantity
        └── timestamps

bundle_categories
├── id (PK)
├── name
├── slug (unique)
├── is_active
├── image_attachment_id (FK → attachments.id)
└── timestamps
```


---

## Customer Management

```
customers
├── id (PK)
├── name
├── email (unique)
├── phone
├── password
├── country_id (FK → countries.id)
├── city_id (FK → cities.id)
├── region_id (FK → regions.id)
├── address
├── points_balance
├── is_active
├── email_verified_at
└── timestamps
    │
    ├─→ customer_addresses
    │   ├── id (PK)
    │   ├── customer_id (FK → customers.id)
    │   ├── country_id (FK → countries.id)
    │   ├── city_id (FK → cities.id)
    │   ├── region_id (FK → regions.id)
    │   ├── address_line_1
    │   ├── address_line_2
    │   ├── postal_code
    │   ├── is_default
    │   └── timestamps
    │
    ├─→ customer_points_transactions
    │   ├── id (PK)
    │   ├── customer_id (FK → customers.id)
    │   ├── transactionable_id (polymorphic)
    │   ├── transactionable_type (Order, Refund, etc.)
    │   ├── type (credit, debit)
    │   ├── points
    │   ├── balance_after
    │   ├── description
    │   └── timestamps
    │
    ├─→ wishlists
    │   └── Links to vendor_products
    │
    ├─→ carts
    │   ├── id (PK)
    │   ├── customer_id (FK → customers.id)
    │   ├── vendor_product_id (FK)
    │   ├── vendor_product_variant_id (FK)
    │   ├── quantity
    │   └── timestamps
    │
    └─→ orders
        └── (See Order Management section)
```


---

## Order Management

### Order System Tree

```
order_stages
├── id (PK)
├── name
├── type (new, processing, shipped, delivered, cancel, refund)
├── sort_number
├── country_id (FK → countries.id)
└── timestamps
    │
    └─→ orders
        ├── id (PK)
        ├── order_number (unique, auto-generated)
        ├── customer_id (FK → customers.id)
        ├── customer_name
        ├── customer_email
        ├── customer_phone
        ├── customer_address
        ├── country_id (FK → countries.id)
        ├── city_id (FK → cities.id)
        ├── region_id (FK → regions.id)
        ├── order_from (web, mobile, admin)
        ├── payment_type (cash, visa, points)
        ├── payment_visa_status
        ├── payment_reference
        ├── customer_promo_code_title
        ├── customer_promo_code_value
        ├── customer_promo_code_type
        ├── customer_promo_code_amount
        ├── shipping (decimal)
        ├── total_tax (decimal)
        ├── total_product_price (decimal)
        ├── items_count
        ├── total_price (decimal)
        ├── total_fees (decimal)
        ├── total_discounts (decimal)
        ├── points_used (decimal)
        ├── points_cost (decimal)
        ├── stage_id (FK → order_stages.id)
        └── timestamps
            │
            ├─→ order_products
            │   ├── id (PK)
            │   ├── order_id (FK → orders.id)
            │   ├── vendor_product_id (FK)
            │   ├── vendor_product_variant_id (FK)
            │   ├── product_name_en
            │   ├── product_name_ar
            │   ├── variant_name_en
            │   ├── variant_name_ar
            │   ├── quantity
            │   ├── price (unit price with tax)
            │   ├── total_price (quantity × price)
            │   ├── commission_rate (%)
            │   ├── commission_amount
            │   ├── stage_id (FK → order_stages.id)
            │   └── timestamps
            │       │
            │       └─→ order_product_taxes
            │           ├── id (PK)
            │           ├── order_product_id (FK)
            │           ├── tax_name_en
            │           ├── tax_name_ar
            │           ├── tax_rate (%)
            │           ├── amount
            │           └── timestamps
            │
            ├─→ order_extra_fees_discounts
            │   ├── id (PK)
            │   ├── order_id (FK → orders.id)
            │   ├── vendor_id (FK → vendors.id)
            │   ├── type (fee, discount)
            │   ├── title
            │   ├── cost
            │   └── timestamps
            │
            ├─→ payments
            │   ├── id (PK)
            │   ├── order_id (FK → orders.id)
            │   ├── payment_method (cash, visa, points)
            │   ├── amount
            │   ├── status (pending, completed, failed)
            │   ├── transaction_id
            │   ├── payment_gateway_response (JSON)
            │   └── timestamps
            │
            └─→ vendor_order_stages
                ├── id (PK)
                ├── order_id (FK → orders.id)
                ├── vendor_id (FK → vendors.id)
                ├── stage_id (FK → order_stages.id)
                ├── total_amount
                ├── commission_amount
                ├── promo_code_share
                ├── points_share
                └── timestamps
                    │
                    └─→ vendor_order_stage_histories
                        ├── id (PK)
                        ├── vendor_order_stage_id (FK)
                        ├── old_stage_id (FK → order_stages.id)
                        ├── new_stage_id (FK → order_stages.id)
                        ├── changed_by_user_id (FK → users.id)
                        ├── notes
                        └── timestamps
```


---

## Refund Management

### Refund System Tree

```
refund_settings (Global)
├── id (PK)
├── customer_pays_return_shipping (boolean)
├── refund_days (integer)
└── timestamps

vendor_refund_settings (Vendor-specific overrides)
├── id (PK)
├── vendor_id (FK → vendors.id)
├── customer_pays_return_shipping (boolean)
├── refund_days (integer)
└── timestamps

refund_requests
├── id (PK)
├── order_id (FK → orders.id)
├── customer_id (FK → customers.id)
├── vendor_id (FK → vendors.id)
├── country_id (FK → countries.id)
├── refund_number (unique, auto-generated)
├── status (pending, approved, in_progress, picked_up, refunded, cancelled)
├── reason (text)
├── customer_notes (text)
├── vendor_notes (text)
├── admin_notes (text)
├── cancellation_reason (text)
├── total_products_amount (decimal)
├── total_shipping_amount (decimal)
├── total_tax_amount (decimal)
├── total_discount_amount (decimal)
├── vendor_fees_amount (decimal)
├── vendor_discounts_amount (decimal)
├── promo_code_amount (decimal)
├── return_shipping_cost (decimal)
├── customer_pays_return_shipping (boolean)
├── points_used (decimal)
├── total_refund_amount (decimal)
├── deleted_at (soft delete)
└── timestamps
    │
    ├─→ refund_request_items
    │   ├── id (PK)
    │   ├── refund_request_id (FK → refund_requests.id)
    │   ├── order_product_id (FK → order_products.id)
    │   ├── quantity
    │   ├── unit_price_without_tax (decimal)
    │   ├── unit_tax_amount (decimal)
    │   ├── total_price_without_tax (decimal)
    │   ├── total_tax_amount (decimal)
    │   ├── total_amount (decimal)
    │   ├── commission_rate (%)
    │   ├── commission_amount (decimal)
    │   └── timestamps
    │
    └─→ refund_request_histories
        ├── id (PK)
        ├── refund_request_id (FK → refund_requests.id)
        ├── old_status
        ├── new_status
        ├── changed_by_user_id (FK → users.id)
        ├── changed_by_customer_id (FK → customers.id)
        ├── notes
        └── timestamps

Refund Workflow:
1. pending → Customer creates refund request
2. approved → Vendor/Admin approves
3. in_progress → Processing return
4. picked_up → Product picked up from customer
5. refunded → Money refunded to customer
6. cancelled → Refund cancelled (only from pending)
```


---

## Accounting & Withdrawals

### Accounting System Tree

```
accounting_entries
├── id (PK)
├── entry_number (unique)
├── entry_date
├── description
├── total_debit (decimal)
├── total_credit (decimal)
├── is_balanced (boolean)
├── country_id (FK → countries.id)
└── timestamps
    │
    └─→ accounting_entry_lines
        ├── id (PK)
        ├── accounting_entry_id (FK)
        ├── account_id (FK → accounts.id)
        ├── debit (decimal)
        ├── credit (decimal)
        ├── description
        └── timestamps

accounts
├── id (PK)
├── account_number (unique)
├── name
├── type (asset, liability, equity, revenue, expense)
├── parent_account_id (FK → accounts.id) [self-referencing]
├── is_active
└── timestamps

vendor_transactions
├── id (PK)
├── vendor_id (FK → vendors.id)
├── transactionable_id (polymorphic)
├── transactionable_type (Order, Refund, Withdraw)
├── type (credit, debit)
├── amount (decimal)
├── balance_after (decimal)
├── description
├── country_id (FK → countries.id)
└── timestamps

customer_points_transactions
├── id (PK)
├── customer_id (FK → customers.id)
├── transactionable_id (polymorphic)
├── transactionable_type (Order, Refund, Admin)
├── type (credit, debit)
├── points (decimal)
├── balance_after (decimal)
├── description
└── timestamps
```


### Withdrawal System Tree

```
withdraw_requests
├── id (PK)
├── vendor_id (FK → vendors.id)
├── request_number (unique)
├── amount (decimal)
├── status (pending, approved, rejected, completed)
├── bank_name
├── bank_account_number
├── iban
├── requested_at
├── approved_at
├── rejected_at
├── completed_at
├── rejection_reason
├── admin_notes
├── country_id (FK → countries.id)
└── timestamps
    │
    └─→ withdraw_request_histories
        ├── id (PK)
        ├── withdraw_request_id (FK)
        ├── old_status
        ├── new_status
        ├── changed_by_user_id (FK → users.id)
        ├── notes
        └── timestamps

Withdrawal Workflow:
1. pending → Vendor creates withdrawal request
2. approved → Admin approves request
3. completed → Money transferred to vendor
4. rejected → Admin rejects request
```

---

## System Settings

```
system_settings
├── id (PK)
├── key (unique)
├── value (JSON or text)
├── type (string, number, boolean, json)
├── group (general, payment, shipping, etc.)
├── is_public (boolean)
└── timestamps

Examples:
• points_per_currency → How many points = 1 currency unit
• commission_rate → Default commission rate
• tax_rate → Default tax rate
• maintenance_mode → Enable/disable site
• payment_gateway_keys → API keys (encrypted)
```


---

## Relationships Overview

### Key Polymorphic Relationships

```
translations (polymorphic)
├── Products → name, description, features, etc.
├── Categories → name, description
├── Brands → name, description
├── Vendors → name, description
├── Taxes → name
└── Any translatable model

attachments (polymorphic)
├── Products → main_image, additional_images, video
├── Vendors → logo, cover
├── Departments → image
├── Categories → image
└── Any model with files

reviews (polymorphic)
├── VendorProducts → customer reviews
└── Can be extended to other reviewable entities

transactionable (polymorphic)
├── VendorTransactions → Order, Refund, Withdraw
└── CustomerPointsTransactions → Order, Refund, Admin
```

### Critical Foreign Key Relationships

```
Country-Based Filtering (Global Scope)
├── All major tables have country_id
├── Automatic filtering by user's country
└── Ensures data isolation per country

Order → Customer → Vendor → Product Flow
orders
├── customer_id → customers
├── order_products
│   ├── vendor_product_id → vendor_products
│   │   ├── vendor_id → vendors
│   │   └── product_id → products
│   └── vendor_product_variant_id → vendor_product_variants
└── vendor_order_stages
    ├── vendor_id → vendors
    └── stage_id → order_stages

Refund → Order → Product Flow
refund_requests
├── order_id → orders
├── customer_id → customers
├── vendor_id → vendors
└── refund_request_items
    └── order_product_id → order_products
```


---

## Indexes & Performance

### Primary Indexes

```sql
-- Unique Indexes
CREATE UNIQUE INDEX idx_users_email ON users(email);
CREATE UNIQUE INDEX idx_vendors_slug ON vendors(slug);
CREATE UNIQUE INDEX idx_products_slug ON products(slug);
CREATE UNIQUE INDEX idx_vendor_products_slug ON vendor_products(slug);
CREATE UNIQUE INDEX idx_orders_order_number ON orders(order_number);
CREATE UNIQUE INDEX idx_refunds_refund_number ON refund_requests(refund_number);

-- Foreign Key Indexes (Auto-created by Laravel)
CREATE INDEX idx_vendor_products_vendor_id ON vendor_products(vendor_id);
CREATE INDEX idx_vendor_products_product_id ON vendor_products(product_id);
CREATE INDEX idx_orders_customer_id ON orders(customer_id);
CREATE INDEX idx_order_products_order_id ON order_products(order_id);
CREATE INDEX idx_refund_requests_order_id ON refund_requests(order_id);

-- Country Filtering (Critical for Performance)
CREATE INDEX idx_vendor_products_country_id ON vendor_products(country_id);
CREATE INDEX idx_orders_country_id ON orders(country_id);
CREATE INDEX idx_customers_country_id ON customers(country_id);
CREATE INDEX idx_vendors_country_id ON vendors(country_id);

-- Status & Active Filters
CREATE INDEX idx_vendor_products_status ON vendor_products(status);
CREATE INDEX idx_vendor_products_is_active ON vendor_products(is_active);
CREATE INDEX idx_orders_stage_id ON orders(stage_id);
CREATE INDEX idx_refund_requests_status ON refund_requests(status);

-- Sorting & Ordering
CREATE INDEX idx_vendor_products_sort_number ON vendor_products(sort_number);
CREATE INDEX idx_departments_sort_number ON departments(sort_number);
CREATE INDEX idx_categories_sort_number ON categories(sort_number);

-- Search Optimization
CREATE FULLTEXT INDEX idx_products_search ON translations(lang_value);
CREATE INDEX idx_vendor_products_sku ON vendor_products(sku);
CREATE INDEX idx_vendor_product_variants_sku ON vendor_product_variants(sku);
```

### Composite Indexes

```sql
-- Product Filtering
CREATE INDEX idx_vendor_products_status_active_country 
ON vendor_products(status, is_active, country_id);

-- Order Queries
CREATE INDEX idx_orders_customer_country_stage 
ON orders(customer_id, country_id, stage_id);

-- Variant Stock Queries
CREATE INDEX idx_variant_stocks_variant_region 
ON variant_stocks(vendor_product_variant_id, region_id);

-- Polymorphic Relationships
CREATE INDEX idx_translations_translatable 
ON translations(translatable_type, translatable_id, lang_id);

CREATE INDEX idx_attachments_attachable 
ON attachments(attachable_type, attachable_id);

CREATE INDEX idx_reviews_reviewable 
ON reviews(reviewable_type, reviewable_id);
```


---

## Database Constraints & Rules

### Soft Deletes

Tables with soft deletes (deleted_at column):
- `vendor_products`
- `refund_requests`
- `vendors`
- `customers`
- `users`
- `products`
- `reviews`

### Cascade Rules

```sql
-- ON DELETE CASCADE
refund_requests → refund_request_items (cascade)
refund_requests → refund_request_histories (cascade)
orders → order_products (cascade)
orders → payments (cascade)
orders → vendor_order_stages (cascade)
vendor_order_stages → vendor_order_stage_histories (cascade)
vendors → vendor_products (cascade)
vendor_products → vendor_product_variants (cascade)
vendor_product_variants → variant_stocks (cascade)

-- ON DELETE RESTRICT (Prevent deletion if referenced)
customers → orders (restrict - cannot delete customer with orders)
vendors → orders (restrict - cannot delete vendor with orders)
products → vendor_products (restrict - cannot delete product in use)
```

### Data Integrity Rules

```sql
-- Check Constraints
ALTER TABLE orders 
ADD CONSTRAINT chk_total_price_positive 
CHECK (total_price >= 0);

ALTER TABLE refund_requests 
ADD CONSTRAINT chk_refund_amount_positive 
CHECK (total_refund_amount >= 0);

ALTER TABLE vendor_product_variants 
ADD CONSTRAINT chk_price_positive 
CHECK (price >= 0);

-- Unique Constraints
ALTER TABLE vendor_products 
ADD CONSTRAINT uq_vendor_product_sku 
UNIQUE (vendor_id, sku);

ALTER TABLE users 
ADD CONSTRAINT uq_users_email 
UNIQUE (email);
```


---

## Complete Entity Relationship Diagram (ERD)

### Master Database Schema Tree

```
┌─────────────────────────────────────────────────────────────────┐
│                        CORE SYSTEM                               │
└─────────────────────────────────────────────────────────────────┘
    │
    ├─→ languages
    │   └─→ translations (polymorphic to all models)
    │
    ├─→ countries
    │   ├─→ cities
    │   │   └─→ regions
    │   │       └─→ shipping_costs
    │   └─→ [Referenced by all major tables]
    │
    └─→ attachments (polymorphic to all models with files)

┌─────────────────────────────────────────────────────────────────┐
│                      USER MANAGEMENT                             │
└─────────────────────────────────────────────────────────────────┘
    │
    ├─→ user_types
    │   └─→ users
    │       ├─→ roles ←→ permissions (many-to-many)
    │       ├─→ activity_logs
    │       ├─→ admin_notifications
    │       │   └─→ admin_notification_views
    │       └─→ vendor_users (pivot to vendors)
    │
    └─→ customers
        ├─→ customer_addresses
        ├─→ customer_points_transactions
        ├─→ wishlists
        ├─→ carts
        ├─→ orders
        └─→ reviews

┌─────────────────────────────────────────────────────────────────┐
│                    VENDOR MANAGEMENT                             │
└─────────────────────────────────────────────────────────────────┘
    │
    └─→ vendors
        ├─→ vendor_users (pivot to users)
        ├─→ vendor_departments (pivot to departments)
        ├─→ vendor_products
        ├─→ vendor_refund_settings
        ├─→ vendor_transactions
        └─→ withdraw_requests
            └─→ withdraw_request_histories

┌─────────────────────────────────────────────────────────────────┐
│                   CATALOG MANAGEMENT                             │
└─────────────────────────────────────────────────────────────────┘
    │
    ├─→ departments
    │   └─→ main_categories
    │       └─→ categories
    │           └─→ sub_categories
    │
    ├─→ brands
    │
    ├─→ taxes
    │
    ├─→ variant_configuration_keys (hierarchical)
    │   └─→ variants_configurations (hierarchical)
    │
    └─→ products
        ├─→ translations (polymorphic)
        ├─→ attachments (polymorphic)
        └─→ vendor_products
            ├─→ vendor_product_taxes (many-to-many with taxes)
            ├─→ vendor_product_variants
            │   ├─→ variant_stocks
            │   ├─→ stock_bookings
            │   └─→ order_fulfillments
            ├─→ reviews (polymorphic)
            ├─→ wishlists
            ├─→ occasion_products
            └─→ bundle_products

┌─────────────────────────────────────────────────────────────────┐
│                   PROMOTIONAL SYSTEMS                            │
└─────────────────────────────────────────────────────────────────┘
    │
    ├─→ promocodes
    │
    ├─→ occasions
    │   └─→ occasion_products
    │       └─→ vendor_product_variants
    │
    └─→ bundle_categories
        └─→ bundles
            └─→ bundle_products
                └─→ vendor_product_variants

┌─────────────────────────────────────────────────────────────────┐
│                     ORDER MANAGEMENT                             │
└─────────────────────────────────────────────────────────────────┘
    │
    ├─→ order_stages
    │
    └─→ orders
        ├─→ order_products
        │   ├─→ order_product_taxes
        │   ├─→ stock_bookings
        │   └─→ order_fulfillments
        ├─→ order_extra_fees_discounts
        ├─→ payments
        └─→ vendor_order_stages
            └─→ vendor_order_stage_histories

┌─────────────────────────────────────────────────────────────────┐
│                    REFUND MANAGEMENT                             │
└─────────────────────────────────────────────────────────────────┘
    │
    ├─→ refund_settings (global)
    │
    ├─→ vendor_refund_settings (vendor-specific)
    │
    └─→ refund_requests
        ├─→ refund_request_items
        │   └─→ order_products
        └─→ refund_request_histories

┌─────────────────────────────────────────────────────────────────┐
│                 ACCOUNTING & WITHDRAWALS                         │
└─────────────────────────────────────────────────────────────────┘
    │
    ├─→ accounts (hierarchical)
    │   └─→ accounting_entries
    │       └─→ accounting_entry_lines
    │
    ├─→ vendor_transactions (polymorphic)
    │
    ├─→ customer_points_transactions (polymorphic)
    │
    └─→ withdraw_requests
        └─→ withdraw_request_histories

┌─────────────────────────────────────────────────────────────────┐
│                      SYSTEM TABLES                               │
└─────────────────────────────────────────────────────────────────┘
    │
    ├─→ system_settings
    ├─→ jobs
    ├─→ job_batches
    ├─→ sessions
    └─→ failed_jobs
```


---

## Key Design Patterns

### 1. Multi-Language Support

**Pattern:** Polymorphic translations table
```
Any Model → translations (polymorphic)
├── translatable_id
├── translatable_type
├── lang_id
├── lang_key (field name)
└── lang_value (translated content)
```

**Benefits:**
- Single table for all translations
- Easy to add new languages
- Flexible field translation

### 2. Multi-Country Operations

**Pattern:** Country-based filtering with global scope
```
All major tables have:
├── country_id (FK → countries.id)
└── Global scope auto-filters by user's country
```

**Benefits:**
- Data isolation per country
- Automatic filtering
- No manual country checks needed

### 3. Hierarchical Variants

**Pattern:** Self-referencing configuration system
```
variant_configuration_keys (self-referencing)
└── variants_configurations (self-referencing)
    └── vendor_product_variants

Example: Color → Size → Material
```

**Benefits:**
- Unlimited nesting levels
- Flexible product configurations
- Supports complex variant trees

### 4. Vendor-Specific Products

**Pattern:** Separation of product definition and vendor offerings
```
products (master data)
└── vendor_products (vendor-specific)
    └── vendor_product_variants (SKU-level)
```

**Benefits:**
- Multiple vendors can sell same product
- Vendor-specific pricing and stock
- Centralized product information


### 5. Order Workflow with Vendor Stages

**Pattern:** Dual-stage system (order-level + vendor-level)
```
orders
├── stage_id (overall order stage)
└── vendor_order_stages
    ├── stage_id (vendor-specific stage)
    └── vendor_order_stage_histories (audit trail)
```

**Benefits:**
- Each vendor can be at different stage
- Independent vendor workflows
- Complete audit trail
- Order stage reflects overall status

### 6. Polymorphic Transactions

**Pattern:** Single transaction table for multiple entities
```
vendor_transactions
├── transactionable_id (polymorphic)
├── transactionable_type (Order, Refund, Withdraw)
├── type (credit, debit)
├── amount
└── balance_after (running balance)
```

**Benefits:**
- Complete financial history
- Easy balance calculation
- Supports any transaction type
- Audit trail built-in

### 7. Soft Deletes with Audit

**Pattern:** Soft delete + activity logging
```
Model (soft delete)
├── deleted_at
└── activity_logs
    ├── action (created, updated, deleted)
    ├── old_values (JSON)
    └── new_values (JSON)
```

**Benefits:**
- Data recovery possible
- Complete audit trail
- Compliance with regulations
- Historical data preserved

### 8. Stock Management with Bookings

**Pattern:** Three-stage stock tracking
```
variant_stocks (available stock)
└── stock_bookings
    ├── status: booked (cart/pending order)
    ├── status: allocated (confirmed order)
    └── status: fulfilled (delivered)
        └── order_fulfillments (delivery tracking)
```

**Benefits:**
- Prevents overselling
- Tracks stock through order lifecycle
- Supports partial fulfillment
- Real-time availability


---

## Data Flow Examples

### 1. Order Creation Flow

```
Customer places order
    ↓
1. Create order record
   ├── order_number (auto-generated)
   ├── customer_id
   ├── total_price
   └── stage_id = 'new'
    ↓
2. Create order_products
   ├── For each cart item
   ├── Store product snapshot (name, price)
   └── Calculate commission
    ↓
3. Create order_product_taxes
   └── Store tax breakdown per product
    ↓
4. Create vendor_order_stages
   ├── One per vendor in order
   ├── Initial stage = 'new'
   └── Calculate vendor totals
    ↓
5. Create stock_bookings
   ├── status = 'booked'
   └── Reserve inventory
    ↓
6. Create payment record
   └── Link to payment gateway
    ↓
7. Create vendor_transactions
   ├── type = 'credit'
   └── Update vendor balance
    ↓
8. Create customer_points_transaction
   ├── If points used: type = 'debit'
   └── If points earned: type = 'credit'
    ↓
9. Send notifications
   ├── Customer: Order confirmation
   ├── Vendor: New order alert
   └── Admin: Order notification
```

### 2. Refund Request Flow

```
Customer requests refund
    ↓
1. Validate refund eligibility
   ├── Check refund_days
   ├── Check order stage
   └── Check already refunded quantity
    ↓
2. Create refund_request
   ├── refund_number (auto-generated)
   ├── status = 'pending'
   └── Calculate refund amounts
    ↓
3. Create refund_request_items
   ├── Link to order_products
   ├── Store quantity to refund
   └── Calculate proportional amounts
    ↓
4. Create refund_request_history
   ├── old_status = null
   ├── new_status = 'pending'
   └── changed_by = customer
    ↓
5. Send notifications
   ├── Vendor: Refund request alert
   └── Admin: Refund notification
    ↓
Vendor/Admin approves
    ↓
6. Update refund_request
   └── status = 'approved'
    ↓
7. Create refund_request_history
   └── Track status change
    ↓
8. Update vendor_order_stage
   └── Change to 'refund' stage
    ↓
Process refund completion
    ↓
9. Update refund_request
   └── status = 'refunded'
    ↓
10. Create vendor_transaction
    ├── type = 'debit'
    └── Deduct from vendor balance
    ↓
11. Create customer_points_transaction
    ├── If points were used
    └── type = 'credit' (return points)
    ↓
12. Update stock_bookings
    └── Release reserved stock
    ↓
13. Send notifications
    └── Customer: Refund completed
```


### 3. Product Variant Creation Flow

```
Admin creates product
    ↓
1. Create product record
   ├── department_id
   ├── category_id
   ├── brand_id
   └── configuration_type = 'variants'
    ↓
2. Create translations
   ├── name (ar, en)
   ├── description (ar, en)
   ├── features (ar, en)
   └── Other translatable fields
    ↓
3. Create attachments
   ├── main_image
   ├── additional_images
   └── video (optional)
    ↓
Vendor adds product to catalog
    ↓
4. Create vendor_product
   ├── vendor_id
   ├── product_id
   ├── sku (vendor-specific)
   ├── points
   └── status = 'pending'
    ↓
5. Link taxes (many-to-many)
   └── vendor_product_taxes
    ↓
6. Create vendor_product_variants
   ├── For each variant combination
   ├── variant_configuration_id
   ├── price
   ├── sku
   └── barcode
    ↓
7. Create variant_stocks
   ├── For each region
   ├── vendor_product_variant_id
   ├── region_id
   └── quantity
    ↓
Admin approves
    ↓
8. Update vendor_product
   └── status = 'approved'
    ↓
9. Send notification
   └── Vendor: Product approved
```


---

## Performance Optimization Strategies

### 1. Caching Strategy

```
Redis Cache Layers:
├── Country data (1 hour TTL)
│   ├── countryapi:*
│   ├── cityapi:*
│   └── regionapi:*
│
├── Product listings (30 min TTL)
│   ├── products:filters:*
│   ├── products:department:*
│   └── products:category:*
│
├── Bundle & Occasion data (1 hour TTL)
│   ├── bundleapi:*
│   ├── bundlecategoryapi:*
│   └── occasionapi:*
│
└── User sessions (24 hours TTL)
    └── laravel_session:*
```

### 2. Query Optimization

```sql
-- Use eager loading to prevent N+1
$products = VendorProduct::with([
    'product.translations',
    'vendor',
    'variants.stocks',
    'taxes'
])->get();

-- Use select() to limit columns
$orders = Order::select('id', 'order_number', 'total_price')
    ->where('customer_id', $customerId)
    ->get();

-- Use chunk() for large datasets
Order::where('created_at', '<', now()->subYear())
    ->chunk(1000, function ($orders) {
        // Process orders
    });

-- Use exists() instead of count()
if (Order::where('customer_id', $id)->exists()) {
    // Customer has orders
}
```

### 3. Index Usage

```sql
-- Composite indexes for common queries
CREATE INDEX idx_vendor_products_lookup 
ON vendor_products(status, is_active, country_id, vendor_id);

-- Covering indexes (include all queried columns)
CREATE INDEX idx_orders_summary 
ON orders(customer_id, country_id, stage_id, total_price, created_at);

-- Partial indexes (MySQL 8.0+)
CREATE INDEX idx_active_products 
ON vendor_products(vendor_id, product_id) 
WHERE is_active = 1 AND status = 'approved';
```

### 4. Database Partitioning (Future)

```sql
-- Partition orders by year
ALTER TABLE orders 
PARTITION BY RANGE (YEAR(created_at)) (
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p2026 VALUES LESS THAN (2027),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);

-- Benefits:
-- • Faster queries on recent data
-- • Easy archival of old data
-- • Better maintenance operations
```


---

## Backup & Recovery Strategy

### Backup Schedule

```
Daily Full Backup
├── Time: 2:00 AM (low traffic)
├── Retention: 7 days
└── Location: Off-site storage

Hourly Incremental Backup
├── Time: Every hour
├── Retention: 24 hours
└── Location: Local + Cloud

Transaction Log Backup
├── Frequency: Every 15 minutes
├── Retention: 48 hours
└── Enables point-in-time recovery
```

### Recovery Procedures

```
1. Point-in-Time Recovery
   ├── Restore latest full backup
   ├── Apply incremental backups
   └── Apply transaction logs up to desired time

2. Table-Level Recovery
   ├── Export specific table from backup
   └── Import to production (careful with FKs)

3. Disaster Recovery
   ├── Failover to replica database
   ├── Promote replica to master
   └── Update application configuration
```

---

## Security Considerations

### 1. Data Encryption

```
Encrypted Fields:
├── users.password (bcrypt)
├── payment_gateway_keys (encrypted)
├── bank_account_number (encrypted)
└── iban (encrypted)

Database Connection:
└── SSL/TLS encryption enabled
```

### 2. Access Control

```
Database Users:
├── app_user (read/write on application tables)
├── readonly_user (read-only for reports)
├── backup_user (backup operations only)
└── admin_user (full access, restricted IP)
```

### 3. Audit Trail

```
Activity Logging:
├── All user actions logged
├── IP address recorded
├── User agent stored
└── Old/new values tracked (JSON)

Critical Operations:
├── Order creation/cancellation
├── Refund requests
├── Withdrawal requests
├── Product approval/rejection
└── User role changes
```


---

## Migration Strategy

### Development to Production

```
1. Version Control
   ├── All migrations in Git
   ├── Sequential numbering
   └── Never modify existing migrations

2. Testing
   ├── Run migrations on staging
   ├── Test rollback procedures
   └── Verify data integrity

3. Deployment
   ├── Backup production database
   ├── Run migrations during maintenance window
   ├── Verify application functionality
   └── Monitor for errors

4. Rollback Plan
   ├── Keep backup accessible
   ├── Test rollback migrations
   └── Document rollback steps
```

### Schema Changes Best Practices

```php
// ✅ Good: Additive changes (safe)
Schema::table('products', function (Blueprint $table) {
    $table->string('new_field')->nullable();
});

// ✅ Good: With default value
Schema::table('products', function (Blueprint $table) {
    $table->boolean('is_featured')->default(false);
});

// ⚠️ Caution: Removing columns (data loss)
Schema::table('products', function (Blueprint $table) {
    $table->dropColumn('old_field'); // Backup first!
});

// ⚠️ Caution: Changing column type (potential data loss)
Schema::table('products', function (Blueprint $table) {
    $table->decimal('price', 10, 2)->change(); // Test thoroughly!
});
```

---

## Monitoring & Maintenance

### Database Health Checks

```sql
-- Check table sizes
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
FROM information_schema.TABLES
WHERE table_schema = 'your_database'
ORDER BY size_mb DESC;

-- Check index usage
SELECT 
    table_name,
    index_name,
    cardinality
FROM information_schema.STATISTICS
WHERE table_schema = 'your_database'
ORDER BY cardinality DESC;

-- Find slow queries
SELECT 
    query_time,
    lock_time,
    rows_examined,
    sql_text
FROM mysql.slow_log
ORDER BY query_time DESC
LIMIT 10;

-- Check replication lag (on replica)
SHOW SLAVE STATUS\G
```

### Maintenance Tasks

```
Daily:
├── Check replication status
├── Monitor disk space
└── Review slow query log

Weekly:
├── Analyze table statistics
├── Optimize tables (if needed)
└── Review index usage

Monthly:
├── Archive old data
├── Review and optimize queries
├── Update database statistics
└── Test backup restoration
```


---

## Summary Statistics

### Database Overview

```
Total Tables: ~80+
├── Core System: 8 tables
├── User Management: 6 tables
├── Vendor Management: 8 tables
├── Catalog Management: 25+ tables
├── Order Management: 12 tables
├── Refund Management: 5 tables
├── Area Settings: 4 tables
├── Accounting: 8 tables
└── System: 5 tables

Total Relationships:
├── One-to-Many: ~120+
├── Many-to-Many: ~15
├── Polymorphic: ~8
└── Self-Referencing: ~4

Indexes:
├── Primary Keys: ~80
├── Foreign Keys: ~150
├── Unique Indexes: ~20
├── Composite Indexes: ~30
└── Full-Text Indexes: ~5
```

### Key Metrics

```
Average Tables per Module: 7-8
Deepest Relationship Chain: 6 levels
  (Country → City → Region → Stock → Variant → Product)

Most Connected Table: vendor_products
  ├── 15+ direct relationships
  └── Central to catalog system

Largest Tables (by row count):
  1. translations (~50K+ rows)
  2. activity_logs (~100K+ rows)
  3. stock_bookings (~200K+ rows)
  4. order_products (~500K+ rows)
  5. orders (~100K+ rows)
```

---

## Conclusion

This database design supports a comprehensive multi-vendor e-commerce platform with:

✅ **Scalability:** Modular design allows horizontal scaling  
✅ **Flexibility:** Polymorphic relationships and hierarchical structures  
✅ **Performance:** Strategic indexing and caching  
✅ **Data Integrity:** Foreign keys, constraints, and soft deletes  
✅ **Audit Trail:** Complete activity logging  
✅ **Multi-tenancy:** Country-based data isolation  
✅ **Internationalization:** Built-in translation support  
✅ **Security:** Encryption, access control, and audit logging  

The schema is designed to handle complex business logic while maintaining data consistency and performance under load.

---

**Document Version:** 1.0  
**Last Updated:** January 29, 2026  
**Maintained By:** Development Team  
**Related Documents:**
- [Project Architecture & Strategy](./PROJECT_ARCHITECTURE_AND_STRATEGY.md)
- [API Documentation](./README_API.md)
- [Database Read/Write Splitting Guide](../.agent/DATABASE_READ_WRITE_SPLITTING_GUIDE.md)
