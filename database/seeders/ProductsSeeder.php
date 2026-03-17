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
use Modules\CatalogManagement\app\Models\VariantsConfiguration;
use App\Models\Translation;
use App\Models\Language;
use App\Models\Attachment;

class ProductsSeeder extends Seeder
{
    private $faker;
    private $languages;
    private $vendors;
    private $brands;
    private $departments;
    private $categories;
    private $subCategories;
    private $regions;
    private $variantConfigurations;
    private $taxes;
    private $firstUserId;

    public function run()
    {
        $this->faker = Faker::create();
        $this->loadRequiredData();
        
        echo "Starting to seed 2000+ products...\n";
        
        // Create 1500 simple products
        $this->createSimpleProducts(1500);
        
        // Create 700 variant products
        $this->createVariantProducts(700);
        
        echo "Seeding completed! Created 2200 products total.\n";
    }

    private function loadRequiredData()
    {
        echo "Loading required data...\n";
        
        $this->languages = Language::all();
        $this->vendors = DB::table('vendors')->where('active', true)->get();
        $this->brands = DB::table('brands')->get();
        $this->departments = DB::table('departments')->get();
        $this->categories = DB::table('categories')->get();
        $this->subCategories = DB::table('sub_categories')->get();
        $this->regions = DB::table('regions')->get();
        $this->variantConfigurations = VariantsConfiguration::with('key')->get();
        $this->taxes = DB::table('taxes')->where('is_active', true)->get();
        $this->firstUserId = DB::table('users')->first()->id;
        
        echo "Data loaded successfully.\n";
    }

    private function createSimpleProducts($count)
    {
        echo "Creating {$count} simple products...\n";
        
        for ($i = 0; $i < $count; $i++) {
            DB::transaction(function () use ($i) {
                // Create main product
                $product = $this->createMainProduct($i, 'simple');
                
                // Create vendor product
                $vendorProduct = $this->createVendorProduct($product);
                
                // Create simple variant
                $this->createSimpleVariant($vendorProduct);
                
                if ($i % 100 == 0) {
                    echo "Created {$i} simple products...\n";
                }
            });
        }
        
        echo "Simple products created successfully!\n";
    }

    private function createVariantProducts($count)
    {
        echo "Creating {$count} variant products...\n";
        
        for ($i = 0; $i < $count; $i++) {
            DB::transaction(function () use ($i) {
                // Create main product
                $product = $this->createMainProduct($i + 1500, 'variants');
                
                // Create vendor product
                $vendorProduct = $this->createVendorProduct($product);
                
                // Create multiple variants
                $this->createProductVariants($vendorProduct);
                
                if ($i % 50 == 0) {
                    echo "Created {$i} variant products...\n";
                }
            });
        }
        
        echo "Variant products created successfully!\n";
    }

    private function createMainProduct($index, $type)
    {
        $vendor = $this->vendors->random();
        $brand = $this->brands->random();
        $department = $this->departments->random();
        $category = $this->categories->random();
        $subCategory = $this->subCategories->random();
        
        $productNames = [
            'Premium Quality Product',
            'Professional Grade Item',
            'High Performance Tool',
            'Advanced Technology Device',
            'Superior Quality Material',
            'Industrial Grade Equipment',
            'Commercial Use Product',
            'Heavy Duty Component',
            'Precision Engineered Part',
            'Durable Construction Item'
        ];
        
        $baseTitle = $this->faker->randomElement($productNames) . ' ' . ($index + 1);
        
        $product = Product::create([
            'slug' => Str::slug($baseTitle) . '-' . $index,
            'is_active' => $this->faker->boolean(90), // 90% active
            'configuration_type' => $type,
            'vendor_id' => $vendor->id,
            'brand_id' => $brand->id,
            'department_id' => $department->id,
            'category_id' => $category->id,
            'sub_category_id' => $subCategory->id,
            'created_by_user_id' => $this->firstUserId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create translations
        $this->createProductTranslations($product, $baseTitle, $index);
        
        // Create images
        $this->createProductImages($product);

        return $product;
    }

    private function createProductTranslations($product, $baseTitle, $index)
    {
        foreach ($this->languages as $language) {
            $isArabic = $language->code === 'ar';
            
            $title = $isArabic ? 
                'منتج عالي الجودة ' . ($index + 1) : 
                $baseTitle;
                
            $details = $isArabic ?
                'تفاصيل المنتج باللغة العربية. هذا منتج عالي الجودة مصمم لتلبية احتياجاتك. يتميز بالمتانة والأداء العالي.' :
                'High-quality product details in English. This premium product is designed to meet your needs with durability and high performance.';
                
            $summary = $isArabic ?
                'ملخص المنتج باللغة العربية' :
                'Product summary in English';
                
            $features = $isArabic ?
                'ميزات المنتج: جودة عالية، متانة فائقة، أداء ممتاز' :
                'Product features: High quality, Superior durability, Excellent performance';

            $translationFields = [
                'title' => $title,
                'details' => $details,
                'summary' => $summary,
                'features' => $features,
                'instructions' => $isArabic ? 'تعليمات الاستخدام' : 'Usage instructions',
                'extra_description' => $isArabic ? 'وصف إضافي' : 'Additional description',
                'material' => $isArabic ? 'مواد عالية الجودة' : 'High-quality materials',
                'tags' => $isArabic ? 'جودة,متانة,أداء' : 'quality,durability,performance',
                'meta_title' => $title,
                'meta_description' => $summary,
                'meta_keywords' => $isArabic ? 'منتج,جودة,متانة' : 'product,quality,durability'
            ];

            foreach ($translationFields as $field => $value) {
                Translation::create([
                    'translatable_type' => Product::class,
                    'translatable_id' => $product->id,
                    'lang_id' => $language->id,
                    'lang_key' => $field,
                    'lang_value' => $value,
                ]);
            }
        }
    }

    private function createProductImages($product)
    {
        // Create main image
        Attachment::create([
            'attachable_type' => Product::class,
            'attachable_id' => $product->id,
            'type' => 'main_image',
            'path' => 'products/images/sample-main-' . $product->id . '.jpg',
        ]);

        // Create 2-4 additional images
        $additionalImagesCount = $this->faker->numberBetween(2, 4);
        for ($i = 0; $i < $additionalImagesCount; $i++) {
            Attachment::create([
                'attachable_type' => Product::class,
                'attachable_id' => $product->id,
                'type' => 'additional_image',
                'path' => 'products/images/sample-additional-' . $product->id . '-' . $i . '.jpg',
            ]);
        }
    }

    private function createVendorProduct($product)
    {
        $vendor = $this->vendors->random();
        
        return VendorProduct::create([
            'vendor_id' => $vendor->id,
            'product_id' => $product->id,
            'sku' => 'SKU-' . strtoupper(Str::random(8)),
            'video_link' => $this->faker->boolean(30) ? $this->faker->url : null,
            'max_per_order' => $this->faker->numberBetween(1, 10),
            'sort_number' => $this->faker->numberBetween(1, 1000),
            'is_active' => $this->faker->boolean(85),
            'is_featured' => $this->faker->boolean(20),
            'is_able_to_refund' => $this->faker->boolean(70),
            'refund_days' => $this->faker->numberBetween(7, 30),
            'status' => $this->faker->randomElement(['approved', 'pending', 'rejected']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createSimpleVariant($vendorProduct)
    {
        $hasDiscount = $this->faker->boolean(30);
        $basePrice = $this->faker->numberBetween(50, 1000);
        
        $variant = VendorProductVariant::create([
            'vendor_product_id' => $vendorProduct->id,
            'sku' => $vendorProduct->sku . '-SIMPLE',
            'price' => $basePrice,
            'has_discount' => $hasDiscount,
            'price_before_discount' => $hasDiscount ? $basePrice + $this->faker->numberBetween(20, 100) : 0,
            'discount_end_date' => $hasDiscount ? $this->faker->dateTimeBetween('now', '+3 months') : null,
            'variant_configuration_id' => null, // Simple products don't have configurations
            'variant_link_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create stocks for multiple regions
        $this->createVariantStocks($variant);
        
        // Sync taxes
        if ($this->taxes->count() > 0) {
            $taxIds = $this->taxes->pluck('id')->toArray();
            $vendorProduct->taxes()->sync($taxIds);
        }

        return $variant;
    }

    private function createProductVariants($vendorProduct)
    {
        // Get random variant configurations for this product
        $configurations = $this->variantConfigurations->where('key_id', '!=', null)->random($this->faker->numberBetween(3, 8));
        
        foreach ($configurations as $config) {
            $hasDiscount = $this->faker->boolean(25);
            $basePrice = $this->faker->numberBetween(75, 1200);
            
            // Try to find a variant link for this configuration
            $variantLinkId = $this->findOrCreateVariantLink($config->id);
            
            $variant = VendorProductVariant::create([
                'vendor_product_id' => $vendorProduct->id,
                'sku' => $vendorProduct->sku . '-VAR-' . $config->id,
                'price' => $basePrice,
                'has_discount' => $hasDiscount,
                'price_before_discount' => $hasDiscount ? $basePrice + $this->faker->numberBetween(30, 150) : 0,
                'discount_end_date' => $hasDiscount ? $this->faker->dateTimeBetween('now', '+6 months') : null,
                'variant_configuration_id' => $config->id,
                'variant_link_id' => $variantLinkId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create stocks
            $this->createVariantStocks($variant);
        }
        
        // Sync taxes
        if ($this->taxes->count() > 0) {
            $taxIds = $this->taxes->pluck('id')->toArray();
            $vendorProduct->taxes()->sync($taxIds);
        }
    }

    private function findOrCreateVariantLink($configId)
    {
        // Try to find existing link
        $existingLink = DB::table('variants_configurations_links')
            ->where('child_config_id', $configId)
            ->first();
            
        if ($existingLink) {
            return $existingLink->id;
        }
        
        // Create a new link if none exists
        $parentConfigs = $this->variantConfigurations->where('id', '!=', $configId)->take(2);
        
        if ($parentConfigs->count() > 0) {
            $parentConfig = $parentConfigs->first();
            
            // Create simple path
            $path = [$parentConfig->id, $configId];
            
            $linkId = DB::table('variants_configurations_links')->insertGetId([
                'parent_config_id' => $parentConfig->id,
                'child_config_id' => $configId,
                'path' => json_encode($path),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            return $linkId;
        }
        
        return null;
    }

    private function createVariantStocks($variant)
    {
        // Create stocks for 1-3 random regions
        $selectedRegions = $this->regions->random($this->faker->numberBetween(1, 3));
        
        foreach ($selectedRegions as $region) {
            VendorProductVariantStock::create([
                'vendor_product_variant_id' => $variant->id,
                'region_id' => $region->id,
                'quantity' => $this->faker->numberBetween(10, 500),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}