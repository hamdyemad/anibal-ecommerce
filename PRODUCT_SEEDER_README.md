# Product Seeders

This document explains how to use the product seeders to generate test data.

## Available Seeders

### 1. ProductsSeeder (Full Seeder)
- **Purpose**: Creates 2000+ products for production-like testing
- **Products**: 1500 simple products + 700 variant products = 2200 total
- **Features**: 
  - Complete product data (translations, images, variants, stocks)
  - Multiple languages support
  - Realistic pricing and discounts
  - Tax associations
  - Variant configurations with links
  - Stock management across regions

### 2. QuickProductsSeeder (Test Seeder)
- **Purpose**: Creates 50 products for quick testing
- **Products**: 30 simple products + 20 variant products = 50 total
- **Features**: Basic product data for development testing

## How to Run

### Quick Test (50 products)
```bash
php artisan db:seed --class=QuickProductsSeeder
```

### Full Seeder (2000+ products)
```bash
php artisan db:seed --class=ProductsSeeder
```

### Run Both
```bash
php artisan db:seed --class=QuickProductsSeeder
php artisan db:seed --class=ProductsSeeder
```

## Prerequisites

Before running the seeders, make sure you have:

1. **Languages**: At least Arabic (ar) and English (en) languages
2. **Vendors**: Active vendors in the system
3. **Brands**: At least a few brands
4. **Departments**: Product departments
5. **Categories**: Main categories
6. **Regions**: At least one region for stock management
7. **Variant Configurations**: Some variant configurations with keys (for variant products)
8. **Taxes**: Active taxes (optional but recommended)

## What Gets Created

### For Each Product:
- **Main Product Record**: Basic product information
- **Translations**: Title, details, summary, features in all languages
- **Images**: Main image + 2-4 additional images (placeholder paths)
- **Vendor Product**: Vendor-specific product data
- **Variants**: 
  - Simple products: 1 variant with no configuration
  - Variant products: 3-8 variants with different configurations
- **Stocks**: Regional stock quantities
- **Tax Associations**: Links to active taxes

### Sample Data Includes:
- **SKUs**: Auto-generated unique SKUs
- **Prices**: Random prices between 50-1200
- **Discounts**: 25-30% of products have discounts
- **Stock Quantities**: 10-500 items per region
- **Product Status**: 85-90% active products
- **Featured Products**: 20-40% featured
- **Refund Settings**: 70% allow refunds

## Performance Notes

- **QuickProductsSeeder**: ~30 seconds
- **ProductsSeeder**: ~10-15 minutes (depending on system)
- Uses database transactions for data integrity
- Progress indicators show completion status

## Customization

You can modify the seeders to:
- Change product quantities
- Adjust price ranges
- Modify discount percentages
- Add more product categories
- Change translation content
- Adjust stock quantities

## Troubleshooting

If you encounter errors:

1. **Missing Dependencies**: Ensure all prerequisite data exists
2. **Memory Issues**: Increase PHP memory limit for large seeders
3. **Timeout Issues**: Increase PHP execution time limit
4. **Database Constraints**: Check foreign key relationships

## Example Usage

```bash
# Quick setup for development
php artisan db:seed --class=QuickProductsSeeder

# Full setup for testing/staging
php artisan db:seed --class=ProductsSeeder

# Check results
php artisan tinker
>>> \Modules\CatalogManagement\app\Models\Product::count()
>>> \Modules\CatalogManagement\app\Models\VendorProduct::count()
>>> \Modules\CatalogManagement\app\Models\VendorProductVariant::count()
```