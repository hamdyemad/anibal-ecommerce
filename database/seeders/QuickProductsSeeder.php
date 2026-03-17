<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Modules\CatalogManagement\app\Models\Product;
use Modules\CatalogManagement\app\Models\VendorProduct;
use Modules\CatalogManagement\app\Models\VendorProductVariant;
use Modules\CatalogManagement\app\Models\VendorProductVariantStock;
use App\Models\Translation;
use App\Models\Language;

class QuickProductsSeeder extends Seeder
{
    /**
     * Quick seeder for testing - creates 50 products (30 simple + 20 variants)
     */
    public function run()
    {
        $faker = Faker::create();
        
        echo "Creating 50 test products...\n";
        
        // Get required data
        $languages = Language::all();
        $vendors = DB::table('vendors')->where('active', true)->get();
        $brands = DB::table('brands')->take(10)->get();
        $departments = DB::table('departments')->take(5)->get();
        $categories = DB::table('categories')->take(10)->get();
        $regions = DB::table('regions')->take(5)->get();
        $firstUserId = DB::table('users')->first()->id;
        
        if ($vendors->isEmpty() || $brands->isEmpty() || $departments->isEmpty() || $categories->isEmpty()) {
            echo "Error: Missing required data (vendors, brands, departments, or categories)\n";
            return;
        }

        // Create 30 simple products
        for ($i = 0; $i < 30; $i++) {
            DB::transaction(function () use ($faker, $languages, $vendors, $brands, $departments, $categories, $regions, $firstUserId, $i) {
                
                $vendor = $vendors->random();
                $brand = $brands->random();
                $department = $departments->random();
                $category = $categories->random();
                
                // Create product
                $product = Product::create([
                    'slug' => 'test-product-' . ($i + 1) . '-' . Str::random(5),
                    'is_active' => true,
                    'configuration_type' => 'simple',
                    'vendor_id' => $vendor->id,
                    'brand_id' => $brand->id,
                    'department_id' => $department->id,
                    'category_id' => $category->id,
                    'sub_category_id' => null,
                    'created_by_user_id' => $firstUserId,
                ]);

                // Create translations
                foreach ($languages as $language) {
                    $isArabic = $language->code === 'ar';
                    $title = $isArabic ? 'منتج تجريبي ' . ($i + 1) : 'Test Product ' . ($i + 1);
                    
                    Translation::create([
                        'translatable_type' => Product::class,
                        'translatable_id' => $product->id,
                        'lang_id' => $language->id,
                        'lang_key' => 'title',
                        'lang_value' => $title,
                    ]);
                    
                    Translation::create([
                        'translatable_type' => Product::class,
                        'translatable_id' => $product->id,
                        'lang_id' => $language->id,
                        'lang_key' => 'details',
                        'lang_value' => $isArabic ? 'تفاصيل المنتج التجريبي' : 'Test product details',
                    ]);
                }

                // Create vendor product
                $vendorProduct = VendorProduct::create([
                    'vendor_id' => $vendor->id,
                    'product_id' => $product->id,
                    'sku' => 'TEST-' . strtoupper(Str::random(6)),
                    'max_per_order' => $faker->numberBetween(1, 5),
                    'sort_number' => $i + 1,
                    'is_active' => true,
                    'is_featured' => $faker->boolean(30),
                    'is_able_to_refund' => true,
                    'refund_days' => 14,
                    'status' => 'approved',
                ]);

                // Create simple variant
                $variant = VendorProductVariant::create([
                    'vendor_product_id' => $vendorProduct->id,
                    'sku' => $vendorProduct->sku . '-SIMPLE',
                    'price' => $faker->numberBetween(100, 500),
                    'has_discount' => false,
                    'price_before_discount' => 0,
                    'discount_end_date' => null,
                    'variant_configuration_id' => null,
                    'variant_link_id' => null,
                ]);

                // Create stock
                VendorProductVariantStock::create([
                    'vendor_product_variant_id' => $variant->id,
                    'region_id' => $regions->random()->id,
                    'quantity' => $faker->numberBetween(50, 200),
                ]);
            });
            
            if ($i % 10 == 0) {
                echo "Created {$i} simple products...\n";
            }
        }

        // Create 20 variant products
        $variantConfigs = DB::table('variants_configurations')->whereNotNull('key_id')->take(20)->get();
        
        for ($i = 30; $i < 50; $i++) {
            DB::transaction(function () use ($faker, $languages, $vendors, $brands, $departments, $categories, $regions, $variantConfigs, $firstUserId, $i) {
                
                $vendor = $vendors->random();
                $brand = $brands->random();
                $department = $departments->random();
                $category = $categories->random();
                
                // Create product
                $product = Product::create([
                    'slug' => 'test-variant-product-' . ($i + 1) . '-' . Str::random(5),
                    'is_active' => true,
                    'configuration_type' => 'variants',
                    'vendor_id' => $vendor->id,
                    'brand_id' => $brand->id,
                    'department_id' => $department->id,
                    'category_id' => $category->id,
                    'sub_category_id' => null,
                    'created_by_user_id' => $firstUserId,
                ]);

                // Create translations
                foreach ($languages as $language) {
                    $isArabic = $language->code === 'ar';
                    $title = $isArabic ? 'منتج متغير تجريبي ' . ($i + 1) : 'Test Variant Product ' . ($i + 1);
                    
                    Translation::create([
                        'translatable_type' => Product::class,
                        'translatable_id' => $product->id,
                        'lang_id' => $language->id,
                        'lang_key' => 'title',
                        'lang_value' => $title,
                    ]);
                }

                // Create vendor product
                $vendorProduct = VendorProduct::create([
                    'vendor_id' => $vendor->id,
                    'product_id' => $product->id,
                    'sku' => 'TEST-VAR-' . strtoupper(Str::random(6)),
                    'max_per_order' => $faker->numberBetween(1, 3),
                    'sort_number' => $i + 1,
                    'is_active' => true,
                    'is_featured' => $faker->boolean(40),
                    'is_able_to_refund' => true,
                    'refund_days' => 14,
                    'status' => 'approved',
                ]);

                // Create 2-4 variants
                $selectedConfigs = $variantConfigs->random($faker->numberBetween(2, 4));
                
                foreach ($selectedConfigs as $config) {
                    $variant = VendorProductVariant::create([
                        'vendor_product_id' => $vendorProduct->id,
                        'sku' => $vendorProduct->sku . '-VAR-' . $config->id,
                        'price' => $faker->numberBetween(150, 600),
                        'has_discount' => $faker->boolean(30),
                        'price_before_discount' => $faker->boolean(30) ? $faker->numberBetween(200, 700) : 0,
                        'discount_end_date' => null,
                        'variant_configuration_id' => $config->id,
                        'variant_link_id' => null, // Will be set when links are created
                    ]);

                    // Create stock
                    VendorProductVariantStock::create([
                        'vendor_product_variant_id' => $variant->id,
                        'region_id' => $regions->random()->id,
                        'quantity' => $faker->numberBetween(20, 100),
                    ]);
                }
            });
            
            if ($i % 10 == 0) {
                echo "Created " . ($i - 29) . " variant products...\n";
            }
        }

        echo "Quick seeding completed! Created 50 test products (30 simple + 20 variants).\n";
    }
}