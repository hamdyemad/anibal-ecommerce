<?php

namespace Modules\CatalogManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CatalogManagement\app\Models\Product;
use Modules\CatalogManagement\app\Models\Brand;
use Modules\CatalogManagement\app\Models\Tax;
use Modules\CatalogManagement\app\Models\ProductVariant;
use Modules\CatalogManagement\app\Models\VariantsConfiguration;
use Modules\CatalogManagement\app\Models\VariantConfigurationKey;
use Modules\CategoryManagment\app\Models\Department;
use Modules\CategoryManagment\app\Models\Category;
use Modules\CategoryManagment\app\Models\SubCategory;
use Modules\CategoryManagment\app\Models\Activity;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Language;

class ProductSeeder extends Seeder
{
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
        DB::transaction(function () {
            // Create Departments
            $departments = $this->seedDepartments();

            // Create Categories and SubCategories
            $categoryData = $this->seedCategories($departments);
            $categories = $categoryData['categories'];
            $subCategories = $categoryData['subCategories'];

            // Create Brands
            $brands = $this->seedBrands();

            // Create Tax
            $taxes = $this->seedTaxes();

            // Create Variant Configuration Keys
            $variantKeys = $this->seedVariantKeys();

            // Create Products with all relationships
            $this->seedProducts($departments, $categories, $subCategories, $brands, $taxes, $variantKeys);

            $this->command->info('Product seeding completed successfully!');
        });
    }

    /**
     * Seed departments
     */
    private function seedDepartments()
    {
        $departments = [
            [
                'name_en' => 'Electronics',
                'name_ar' => 'الإلكترونيات',
                'description_en' => 'Electronic devices and gadgets',
                'description_ar' => 'الأجهزة الإلكترونية والملحقات',
                'active' => true,
            ],
            [
                'name_en' => 'Fashion',
                'name_ar' => 'الموضة',
                'description_en' => 'Clothing and fashion items',
                'description_ar' => 'الملابس والأزياء',
                'active' => true,
            ],
            [
                'name_en' => 'Home & Garden',
                'name_ar' => 'المنزل والحديقة',
                'description_en' => 'Home and garden products',
                'description_ar' => 'منتجات المنزل والحديقة',
                'active' => true,
            ],
        ];

        $createdDepartments = [];
        $englishLangId = $this->getLanguageId('en');
        $arabicLangId = $this->getLanguageId('ar');

        foreach ($departments as $dept) {
            // Use firstOrCreate to prevent duplicates - use name as identifier
            $department = Department::firstOrCreate(
                ['id' => Department::count() + 1], // Fallback approach
                ['active' => $dept['active']]
            );

            // Check if translations already exist
            $englishNameExists = $department->translations()
                ->where('lang_id', $englishLangId)
                ->where('lang_key', 'name')
                ->exists();

            if (!$englishNameExists) {
                // Store translations only if they don't exist
                $department->translations()->create([
                    'lang_id' => $englishLangId,
                    'lang_key' => 'name',
                    'lang_value' => $dept['name_en'],
                ]);

                $department->translations()->create([
                    'lang_id' => $englishLangId,
                    'lang_key' => 'description',
                    'lang_value' => $dept['description_en'],
                ]);

                $department->translations()->create([
                    'lang_id' => $arabicLangId,
                    'lang_key' => 'name',
                    'lang_value' => $dept['name_ar'],
                ]);

                $department->translations()->create([
                    'lang_id' => $arabicLangId,
                    'lang_key' => 'description',
                    'lang_value' => $dept['description_ar'],
                ]);
            }

            $createdDepartments[] = $department;
        }

        $this->command->info('Created ' . count($createdDepartments) . ' departments');
        return $createdDepartments;
    }

    /**
     * Seed categories and subcategories
     */
    private function seedCategories($departments)
    {
        $categories = [];
        $subCategories = [];
        $englishLangId = $this->getLanguageId('en');
        $arabicLangId = $this->getLanguageId('ar');

        // Electronics categories
        $electronicsCategories = [
            [
                'name_en' => 'Smartphones',
                'name_ar' => 'الهواتف الذكية',
                'department_id' => $departments[0]->id,
                'active' => true,
            ],
            [
                'name_en' => 'Laptops',
                'name_ar' => 'أجهزة الكمبيوتر المحمولة',
                'department_id' => $departments[0]->id,
                'active' => true,
            ],
        ];

        // Fashion categories
        $fashionCategories = [
            [
                'name_en' => 'Men',
                'name_ar' => 'الرجال',
                'department_id' => $departments[1]->id,
                'active' => true,
            ],
            [
                'name_en' => 'Women',
                'name_ar' => 'النساء',
                'department_id' => $departments[1]->id,
                'active' => true,
            ],
        ];

        // Home & Garden categories
        $homeCategories = [
            [
                'name_en' => 'Furniture',
                'name_ar' => 'الأثاث',
                'department_id' => $departments[2]->id,
                'active' => true,
            ],
        ];

        $allCategories = array_merge($electronicsCategories, $fashionCategories, $homeCategories);

        foreach ($allCategories as $cat) {
            $category = Category::create([
                'department_id' => $cat['department_id'],
                'active' => $cat['active'],
            ]);

            // Add translations
            $category->translations()->create([
                'lang_id' => $englishLangId,
                'lang_key' => 'name',
                'lang_value' => $cat['name_en'],
            ]);

            $category->translations()->create([
                'lang_id' => $arabicLangId,
                'lang_key' => 'name',
                'lang_value' => $cat['name_ar'],
            ]);

            $categories[] = $category;
        }

        // Create additional categories (no subcategories in this schema)
        $additionalCategories = [
            [
                'name_en' => 'Android Phones',
                'name_ar' => 'هواتف أندرويد',
                'department_id' => $departments[0]->id,
                'active' => true,
            ],
            [
                'name_en' => 'Gaming Laptops',
                'name_ar' => 'أجهزة كمبيوتر الألعاب',
                'department_id' => $departments[0]->id,
                'active' => true,
            ],
            [
                'name_en' => 'Shirts',
                'name_ar' => 'القمصان',
                'department_id' => $departments[1]->id,
                'active' => true,
            ],
            [
                'name_en' => 'Dresses',
                'name_ar' => 'الفساتين',
                'department_id' => $departments[1]->id,
                'active' => true,
            ],
            [
                'name_en' => 'Sofas',
                'name_ar' => 'الأرائك',
                'department_id' => $departments[2]->id,
                'active' => true,
            ],
        ];

        foreach ($additionalCategories as $cat) {
            $category = Category::create([
                'department_id' => $cat['department_id'],
                'active' => $cat['active'],
            ]);

            // Add translations
            $category->translations()->create([
                'lang_id' => $englishLangId,
                'lang_key' => 'name',
                'lang_value' => $cat['name_en'],
            ]);

            $category->translations()->create([
                'lang_id' => $arabicLangId,
                'lang_key' => 'name',
                'lang_value' => $cat['name_ar'],
            ]);

            $categories[] = $category;
        }

        // Create subcategories for products
        $subCategoryData = [
            [
                'name_en' => 'Samsung Phones',
                'name_ar' => 'هواتف سامسونج',
                'category_id' => $categories[0]->id,
            ],
            [
                'name_en' => 'Dell Laptops',
                'name_ar' => 'أجهزة ديل',
                'category_id' => $categories[1]->id,
            ],
            [
                'name_en' => 'Sports Shoes',
                'name_ar' => 'أحذية رياضية',
                'category_id' => $categories[2]->id,
            ],
            [
                'name_en' => 'Modern Furniture',
                'name_ar' => 'أثاث حديث',
                'category_id' => $categories[4]->id,
            ],
        ];

        foreach ($subCategoryData as $subCat) {
            $subCategory = SubCategory::create([
                'category_id' => $subCat['category_id'],
            ]);

            $subCategory->translations()->create([
                'lang_id' => $englishLangId,
                'lang_key' => 'name',
                'lang_value' => $subCat['name_en'],
            ]);

            $subCategory->translations()->create([
                'lang_id' => $arabicLangId,
                'lang_key' => 'name',
                'lang_value' => $subCat['name_ar'],
            ]);

            $subCategories[] = $subCategory;
        }

        $this->command->info('Created ' . count($categories) . ' categories');
        $this->command->info('Created ' . count($subCategories) . ' subcategories');

        return [
            'categories' => $categories,
            'subCategories' => $subCategories,
        ];
    }

    /**
     * Seed brands
     */
    private function seedBrands()
    {
        $brands = [
            [
                'name_en' => 'Samsung',
                'name_ar' => 'سامسونج',
                'slug' => 'samsung',
                'is_active' => true,
            ],
            [
                'name_en' => 'Apple',
                'name_ar' => 'أبل',
                'slug' => 'apple',
                'is_active' => true,
            ],
            [
                'name_en' => 'Dell',
                'name_ar' => 'ديل',
                'slug' => 'dell',
                'is_active' => true,
            ],
            [
                'name_en' => 'Nike',
                'name_ar' => 'نايك',
                'slug' => 'nike',
                'is_active' => true,
            ],
            [
                'name_en' => 'IKEA',
                'name_ar' => 'إيكيا',
                'slug' => 'ikea',
                'is_active' => true,
            ],
        ];

        $createdBrands = [];
        $englishLangId = $this->getLanguageId('en');
        $arabicLangId = $this->getLanguageId('ar');

        foreach ($brands as $brand) {
            // Use firstOrCreate to prevent duplicates
            $brandModel = Brand::firstOrCreate(
                ['slug' => $brand['slug']],
                ['active' => $brand['is_active']]
            );

            // Check if translations already exist
            $englishNameExists = $brandModel->translations()
                ->where('lang_id', $englishLangId)
                ->where('lang_key', 'name')
                ->exists();

            if (!$englishNameExists) {
                // Add translations only if they don't exist
                $brandModel->translations()->create([
                    'lang_id' => $englishLangId,
                    'lang_key' => 'name',
                    'lang_value' => $brand['name_en'],
                ]);

                $brandModel->translations()->create([
                    'lang_id' => $arabicLangId,
                    'lang_key' => 'name',
                    'lang_value' => $brand['name_ar'],
                ]);
            }

            $createdBrands[] = $brandModel;
        }

        $this->command->info('Created ' . count($createdBrands) . ' brands');
        return $createdBrands;
    }

    /**
     * Seed taxes
     */
    private function seedTaxes()
    {
        $taxes = [
            [
                'name_en' => 'Standard Tax',
                'name_ar' => 'الضريبة القياسية',
                'slug' => 'standard-tax',
                'tax_rate' => 15.00,
                'active' => true,
            ],
            [
                'name_en' => 'Reduced Tax',
                'name_ar' => 'الضريبة المخفضة',
                'slug' => 'reduced-tax',
                'tax_rate' => 5.00,
                'active' => true,
            ],
            [
                'name_en' => 'Premium Tax',
                'name_ar' => 'ضريبة الفئة الممتازة',
                'slug' => 'premium-tax',
                'tax_rate' => 20.00,
                'active' => true,
            ],
        ];

        $createdTaxes = [];
        $englishLangId = $this->getLanguageId('en');
        $arabicLangId = $this->getLanguageId('ar');

        foreach ($taxes as $tax) {
            // Use firstOrCreate to prevent duplicates
            $taxModel = Tax::firstOrCreate(
                ['slug' => $tax['slug']],
                [
                    'tax_rate' => $tax['tax_rate'],
                    'active' => $tax['active'],
                ]
            );

            // Check if translations already exist
            $englishNameExists = $taxModel->translations()
                ->where('lang_id', $englishLangId)
                ->where('lang_key', 'name')
                ->exists();

            if (!$englishNameExists) {
                // Add translations only if they don't exist
                $taxModel->translations()->create([
                    'lang_id' => $englishLangId,
                    'lang_key' => 'name',
                    'lang_value' => $tax['name_en'],
                ]);

                $taxModel->translations()->create([
                    'lang_id' => $arabicLangId,
                    'lang_key' => 'name',
                    'lang_value' => $tax['name_ar'],
                ]);
            }

            $createdTaxes[] = $taxModel;
        }

        $this->command->info('Created ' . count($createdTaxes) . ' taxes');
        return $createdTaxes;
    }

    /**
     * Seed variant configuration keys
     */
    private function seedVariantKeys()
    {
        $keys = [
            [
                'name_en' => 'Color',
                'name_ar' => 'اللون',
            ],
            [
                'name_en' => 'Size',
                'name_ar' => 'الحجم',
            ],
            [
                'name_en' => 'Storage',
                'name_ar' => 'التخزين',
            ],
        ];

        $createdKeys = [];
        $englishLangId = $this->getLanguageId('en');
        $arabicLangId = $this->getLanguageId('ar');

        foreach ($keys as $key) {
            // For variant keys, we'll create them if they don't exist by checking translations
            $existingKey = VariantConfigurationKey::whereHas('translations', function ($query) use ($englishLangId, $key) {
                $query->where('lang_id', $englishLangId)
                    ->where('lang_key', 'name')
                    ->where('lang_value', $key['name_en']);
            })->first();

            if ($existingKey) {
                $keyModel = $existingKey;
            } else {
                $keyModel = VariantConfigurationKey::create([]);

                // Add translations
                $keyModel->translations()->create([
                    'lang_id' => $englishLangId,
                    'lang_key' => 'name',
                    'lang_value' => $key['name_en'],
                ]);

                $keyModel->translations()->create([
                    'lang_id' => $arabicLangId,
                    'lang_key' => 'name',
                    'lang_value' => $key['name_ar'],
                ]);
            }

            $createdKeys[] = $keyModel;
        }

        $this->command->info('Created ' . count($createdKeys) . ' variant keys');
        return $createdKeys;
    }

    /**
     * Seed products with all relationships
     */
    private function seedProducts($departments, $categories, $subCategories, $brands, $taxes, $variantKeys)
    {
        $englishLangId = $this->getLanguageId('en');
        $arabicLangId = $this->getLanguageId('ar');

        // Get first user for created_by_user_id
        $user = \App\Models\User::first();
        if (!$user) {
            $this->command->error('No user found. Please create a user first.');
            return;
        }

        // Get first vendor for vendor_id
        $vendor = \Modules\Vendor\app\Models\Vendor::first();

        $products = [
            // Electronics - Smartphones
            [
                'title_en' => 'Samsung Galaxy S21',
                'title_ar' => 'سامسونج جالاكسي S21',
                'slug' => 'samsung-galaxy-s21',
                'brand_id' => $brands[0]->id,
                'department_id' => $departments[0]->id,
                'category_id' => $categories[0]->id,
                'sub_category_id' => $subCategories[0]->id,
                'is_active' => true,
            ],
            [
                'title_en' => 'Samsung Galaxy A52',
                'title_ar' => 'سامسونج جالاكسي A52',
                'slug' => 'samsung-galaxy-a52',
                'brand_id' => $brands[0]->id,
                'department_id' => $departments[0]->id,
                'category_id' => $categories[0]->id,
                'sub_category_id' => $subCategories[0]->id,
                'is_active' => true,
            ],
            [
                'title_en' => 'Apple iPhone 13',
                'title_ar' => 'أبل آيفون 13',
                'slug' => 'apple-iphone-13',
                'brand_id' => $brands[1]->id,
                'department_id' => $departments[0]->id,
                'category_id' => $categories[0]->id,
                'sub_category_id' => $subCategories[0]->id,
                'is_active' => true,
            ],
            [
                'title_en' => 'Apple iPhone 12',
                'title_ar' => 'أبل آيفون 12',
                'slug' => 'apple-iphone-12',
                'brand_id' => $brands[1]->id,
                'department_id' => $departments[0]->id,
                'category_id' => $categories[0]->id,
                'sub_category_id' => $subCategories[0]->id,
                'is_active' => true,
            ],
            // Electronics - Laptops
            [
                'title_en' => 'Dell XPS 13 Laptop',
                'title_ar' => 'جهاز كمبيوتر ديل XPS 13',
                'slug' => 'dell-xps-13-laptop',
                'brand_id' => $brands[2]->id,
                'department_id' => $departments[0]->id,
                'category_id' => $categories[1]->id,
                'sub_category_id' => $subCategories[1]->id,
                'is_active' => true,
            ],
            [
                'title_en' => 'Dell Inspiron 15',
                'title_ar' => 'ديل إنسبايرون 15',
                'slug' => 'dell-inspiron-15',
                'brand_id' => $brands[2]->id,
                'department_id' => $departments[0]->id,
                'category_id' => $categories[1]->id,
                'sub_category_id' => $subCategories[1]->id,
                'is_active' => true,
            ],
            [
                'title_en' => 'Apple MacBook Pro 14',
                'title_ar' => 'أبل ماك بوك برو 14',
                'slug' => 'apple-macbook-pro-14',
                'brand_id' => $brands[1]->id,
                'department_id' => $departments[0]->id,
                'category_id' => $categories[1]->id,
                'sub_category_id' => $subCategories[1]->id,
                'is_active' => true,
            ],
            [
                'title_en' => 'Apple MacBook Air M1',
                'title_ar' => 'أبل ماك بوك إير M1',
                'slug' => 'apple-macbook-air-m1',
                'brand_id' => $brands[1]->id,
                'department_id' => $departments[0]->id,
                'category_id' => $categories[1]->id,
                'sub_category_id' => $subCategories[1]->id,
                'is_active' => true,
            ],
            // Fashion - Men
            [
                'title_en' => 'Nike Running Shoes',
                'title_ar' => 'حذاء الجري من نايك',
                'slug' => 'nike-running-shoes',
                'brand_id' => $brands[3]->id,
                'department_id' => $departments[1]->id,
                'category_id' => $categories[2]->id,
                'sub_category_id' => $subCategories[2]->id,
                'is_active' => true,
            ],
            [
                'title_en' => 'Nike Air Max 90',
                'title_ar' => 'نايك إير ماكس 90',
                'slug' => 'nike-air-max-90',
                'brand_id' => $brands[3]->id,
                'department_id' => $departments[1]->id,
                'category_id' => $categories[2]->id,
                'sub_category_id' => $subCategories[2]->id,
                'is_active' => true,
            ],
            [
                'title_en' => 'Nike Court Legacy',
                'title_ar' => 'نايك كورت ليجاسي',
                'slug' => 'nike-court-legacy',
                'brand_id' => $brands[3]->id,
                'department_id' => $departments[1]->id,
                'category_id' => $categories[2]->id,
                'sub_category_id' => $subCategories[2]->id,
                'is_active' => true,
            ],
            [
                'title_en' => 'Nike Revolution 6',
                'title_ar' => 'نايك ريفولوشن 6',
                'slug' => 'nike-revolution-6',
                'brand_id' => $brands[3]->id,
                'department_id' => $departments[1]->id,
                'category_id' => $categories[2]->id,
                'sub_category_id' => $subCategories[2]->id,
                'is_active' => true,
            ],
            // Fashion - Women
            [
                'title_en' => 'Nike Womens Revolution 6',
                'title_ar' => 'نايك ريفولوشن 6 للنساء',
                'slug' => 'nike-womens-revolution-6',
                'brand_id' => $brands[3]->id,
                'department_id' => $departments[1]->id,
                'category_id' => $categories[3]->id,
                'sub_category_id' => $subCategories[2]->id,
                'is_active' => true,
            ],
            [
                'title_en' => 'Nike Womens Air Max 270',
                'title_ar' => 'نايك إير ماكس 270 للنساء',
                'slug' => 'nike-womens-air-max-270',
                'brand_id' => $brands[3]->id,
                'department_id' => $departments[1]->id,
                'category_id' => $categories[3]->id,
                'sub_category_id' => $subCategories[2]->id,
                'is_active' => true,
            ],
            // Home & Garden - Furniture
            [
                'title_en' => 'IKEA Modern Sofa',
                'title_ar' => 'أريكة إيكيا الحديثة',
                'slug' => 'ikea-modern-sofa',
                'brand_id' => $brands[4]->id,
                'department_id' => $departments[2]->id,
                'category_id' => $categories[4]->id,
                'sub_category_id' => $subCategories[3]->id,
                'is_active' => true,
            ],
            [
                'title_en' => 'IKEA Sectional Sofa',
                'title_ar' => 'أريكة إيكيا زاوية',
                'slug' => 'ikea-sectional-sofa',
                'brand_id' => $brands[4]->id,
                'department_id' => $departments[2]->id,
                'category_id' => $categories[4]->id,
                'sub_category_id' => $subCategories[3]->id,
                'is_active' => true,
            ],
            [
                'title_en' => 'IKEA Dining Table',
                'title_ar' => 'طاولة طعام إيكيا',
                'slug' => 'ikea-dining-table',
                'brand_id' => $brands[4]->id,
                'department_id' => $departments[2]->id,
                'category_id' => $categories[4]->id,
                'sub_category_id' => $subCategories[3]->id,
                'is_active' => true,
            ],
            [
                'title_en' => 'IKEA Office Chair',
                'title_ar' => 'كرسي مكتب إيكيا',
                'slug' => 'ikea-office-chair',
                'brand_id' => $brands[4]->id,
                'department_id' => $departments[2]->id,
                'category_id' => $categories[4]->id,
                'sub_category_id' => $subCategories[3]->id,
                'is_active' => true,
            ],
        ];

        $createdProducts = 0;
        $skippedProducts = 0;

        foreach ($products as $productData) {
            // Use firstOrCreate to prevent duplicates
            $product = Product::firstOrCreate(
                ['slug' => $productData['slug']],
                [
                    'brand_id' => $productData['brand_id'],
                    'department_id' => $productData['department_id'],
                    'category_id' => $productData['category_id'],
                    'sub_category_id' => $productData['sub_category_id'],
                    'vendor_id' => $vendor?->id,
                    'created_by_user_id' => $user->id,
                    'is_active' => $productData['is_active'],
                    'configuration_type' => 'simple',
                ]
            );

            // Check if product was just created (wasRecentlyCreated)
            if ($product->wasRecentlyCreated) {
                // Add translations only for new products
                $product->translations()->create([
                    'lang_id' => $englishLangId,
                    'lang_key' => 'title',
                    'lang_value' => $productData['title_en'],
                ]);

                $product->translations()->create([
                    'lang_id' => $arabicLangId,
                    'lang_key' => 'title',
                    'lang_value' => $productData['title_ar'],
                ]);

                $createdProducts++;
                $this->command->info('Created product: ' . $productData['title_en']);
            } else {
                $skippedProducts++;
                $this->command->info('Skipped (already exists): ' . $productData['title_en']);
            }
        }

        $this->command->info('Created ' . $createdProducts . ' new products');
        $this->command->info('Skipped ' . $skippedProducts . ' duplicate products');
    }
}
