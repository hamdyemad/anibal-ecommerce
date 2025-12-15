<?php

namespace Modules\CatalogManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CatalogManagement\app\Models\BundleCategory;
use Modules\CatalogManagement\app\Models\Bundle;
use Modules\CatalogManagement\app\Models\BundleProduct;
use Modules\CatalogManagement\app\Models\VendorProductVariant;
use Modules\Vendor\app\Models\Vendor;
use App\Models\Language;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BundleSeeder extends Seeder
{
    private $englishLangId;
    private $arabicLangId;
    private $vendor;

    /**
     * Get language ID by code
     */
    private function getLanguageId(string $code): int
    {
        $language = Language::where('code', $code)->first();
        if (!$language) {
            throw new \Exception("Language with code '{$code}' not found");
        }
        return $language->id;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Initialize
        $this->englishLangId = $this->getLanguageId('en');
        $this->arabicLangId = $this->getLanguageId('ar');
        $this->vendor = Vendor::first();

        if (!$this->vendor) {
            $this->command->error('No vendor found');
            return;
        }

        // Check if vendor product variants exist
        $variantCount = VendorProductVariant::count();
        if ($variantCount === 0) {
            $this->command->error('No vendor product variants found. Please run ProductSeeder first.');
            return;
        }

        // Clear existing data
        $this->clearExistingData();

        // Create Bundle Categories
        $bundleCategories = $this->seedBundleCategories();

        // Create Bundles with products
        $this->seedBundles($bundleCategories);

        $this->command->info('✓ Bundle seeding completed successfully!');
    }

    /**
     * Clear existing bundle data
     */
    private function clearExistingData(): void
    {
        $this->command->info('Clearing existing bundle data...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('bundle_products')->truncate();
        DB::table('bundles')->truncate();
        DB::table('bundle_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('✓ Cleared existing data');
    }

    /**
     * Seed bundle categories
     */
    private function seedBundleCategories()
    {
        $categories = [
            ['name_en' => 'Electronics Bundle', 'name_ar' => 'حزمة الإلكترونيات'],
            ['name_en' => 'Fashion Bundle', 'name_ar' => 'حزمة الموضة'],
            ['name_en' => 'Home Bundle', 'name_ar' => 'حزمة المنزل'],
            ['name_en' => 'Office Bundle', 'name_ar' => 'حزمة المكتب'],
        ];

        $created = [];
        foreach ($categories as $cat) {
            $category = BundleCategory::create([
                'country_id' => $this->vendor->country_id,
                'slug' => Str::slug($cat['name_en']),
                'active' => true,
            ]);

            $category->translations()->create([
                'lang_id' => $this->englishLangId,
                'lang_key' => 'name',
                'lang_value' => $cat['name_en'],
            ]);

            $category->translations()->create([
                'lang_id' => $this->arabicLangId,
                'lang_key' => 'name',
                'lang_value' => $cat['name_ar'],
            ]);

            $created[] = $category;
            $this->command->info("✓ Created bundle category: {$cat['name_en']}");
        }

        return $created;
    }

    /**
     * Seed bundles with products
     */
    private function seedBundles($bundleCategories)
    {
        // Get vendor product variants
        $variants = VendorProductVariant::with('vendorProduct.product')
            ->inRandomOrder()
            ->limit(20)
            ->get();

        if ($variants->isEmpty()) {
            $this->command->error('No vendor product variants available');
            return;
        }

        $bundles = [
            [
                'name_en' => 'Complete Tech Setup',
                'name_ar' => 'إعداد التكنولوجيا الكامل',
                'description_en' => 'Everything you need for a complete tech experience',
                'description_ar' => 'كل ما تحتاجه لتجربة تكنولوجية كاملة',
                'category' => 0,
                'sku' => 'BUNDLE-TECH-001',
                'products_count' => 3,
            ],
            [
                'name_en' => 'Fashion Starter Pack',
                'name_ar' => 'حزمة بدء الموضة',
                'description_en' => 'Essential fashion items for your wardrobe',
                'description_ar' => 'عناصر الموضة الأساسية لخزانة ملابسك',
                'category' => 1,
                'sku' => 'BUNDLE-FASHION-001',
                'products_count' => 4,
            ],
            [
                'name_en' => 'Home Comfort Bundle',
                'name_ar' => 'حزمة راحة المنزل',
                'description_en' => 'Make your home more comfortable',
                'description_ar' => 'اجعل منزلك أكثر راحة',
                'category' => 2,
                'sku' => 'BUNDLE-HOME-001',
                'products_count' => 3,
            ],
            [
                'name_en' => 'Office Productivity Bundle',
                'name_ar' => 'حزمة إنتاجية المكتب',
                'description_en' => 'Boost your office productivity',
                'description_ar' => 'عزز إنتاجيتك في المكتب',
                'category' => 3,
                'sku' => 'BUNDLE-OFFICE-001',
                'products_count' => 3,
            ],
            [
                'name_en' => 'Premium Tech Bundle',
                'name_ar' => 'حزمة التكنولوجيا المتميزة',
                'description_en' => 'Premium technology products bundle',
                'description_ar' => 'حزمة منتجات التكنولوجيا المتميزة',
                'category' => 0,
                'sku' => 'BUNDLE-TECH-PREMIUM-001',
                'products_count' => 4,
            ],
        ];

        $createdCount = 0;
        foreach ($bundles as $bundleData) {
            $bundle = Bundle::create([
                'vendor_id' => $this->vendor->id,
                'country_id' => $this->vendor->country_id,
                'bundle_category_id' => $bundleCategories[$bundleData['category']]->id,
                'sku' => $bundleData['sku'],
                'slug' => Str::slug($bundleData['name_en']),
                'is_active' => true,
                'admin_approval' => true,
            ]);

            // Add translations
            $bundle->translations()->create([
                'lang_id' => $this->englishLangId,
                'lang_key' => 'name',
                'lang_value' => $bundleData['name_en'],
            ]);

            $bundle->translations()->create([
                'lang_id' => $this->arabicLangId,
                'lang_key' => 'name',
                'lang_value' => $bundleData['name_ar'],
            ]);

            $bundle->translations()->create([
                'lang_id' => $this->englishLangId,
                'lang_key' => 'description',
                'lang_value' => $bundleData['description_en'],
            ]);

            $bundle->translations()->create([
                'lang_id' => $this->arabicLangId,
                'lang_key' => 'description',
                'lang_value' => $bundleData['description_ar'],
            ]);

            // Add bundle products
            $selectedVariants = $variants->random(min($bundleData['products_count'], $variants->count()));
            $basePrice = 0;
            $position = 0;

            foreach ($selectedVariants as $variant) {
                $price = $variant->price ?? 100;
                $basePrice += $price;

                BundleProduct::create([
                    'bundle_id' => $bundle->id,
                    'vendor_product_variant_id' => $variant->id,
                    'price' => $price,
                    'limitation_quantity' => rand(1, 5),
                    'min_quantity' => 1,
                ]);

                $position++;
            }

            $createdCount++;
            $this->command->info("✓ Created bundle: {$bundleData['name_en']} with {$bundleData['products_count']} products");
        }

        $this->command->info("✓ Created {$createdCount} bundles");
    }
}
