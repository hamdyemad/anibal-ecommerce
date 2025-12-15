<?php

namespace Modules\CatalogManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CatalogManagement\app\Models\Occasion;
use Modules\CatalogManagement\app\Models\OccasionProduct;
use Modules\CatalogManagement\app\Models\VendorProductVariant;
use Modules\Vendor\app\Models\Vendor;
use App\Models\Language;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OccasionSeeder extends Seeder
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

        // Create Occasions
        $this->seedOccasions();

        $this->command->info('✓ Occasion seeding completed successfully!');
    }

    /**
     * Clear existing occasion data
     */
    private function clearExistingData(): void
    {
        $this->command->info('Clearing existing occasion data...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('occasion_products')->truncate();
        DB::table('occasions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('✓ Cleared existing data');
    }

    /**
     * Seed occasions with products
     */
    private function seedOccasions()
    {
        // Get vendor product variants
        $variants = VendorProductVariant::with('vendorProduct.product')
            ->inRandomOrder()
            ->limit(30)
            ->get();

        if ($variants->isEmpty()) {
            $this->command->error('No vendor product variants available');
            return;
        }

        $occasions = [
            [
                'name_en' => 'New Year Celebration',
                'name_ar' => 'احتفال السنة الجديدة',
                'title_en' => 'Ring in the New Year',
                'title_ar' => 'استقبل السنة الجديدة',
                'subtitle_en' => 'Special offers for New Year',
                'subtitle_ar' => 'عروض خاصة للسنة الجديدة',
                'start_date' => now()->addDays(5),
                'end_date' => now()->addDays(35),
                'products_count' => 4,
            ],
            [
                'name_en' => 'Valentine\'s Day',
                'name_ar' => 'عيد الحب',
                'title_en' => 'Love is in the Air',
                'title_ar' => 'الحب في الهواء',
                'subtitle_en' => 'Perfect gifts for your loved ones',
                'subtitle_ar' => 'هدايا مثالية لأحبائك',
                'start_date' => now()->addDays(60),
                'end_date' => now()->addDays(75),
                'products_count' => 3,
            ],
            [
                'name_en' => 'Summer Sale',
                'name_ar' => 'تخفيضات الصيف',
                'title_en' => 'Hot Summer Deals',
                'title_ar' => 'صفقات صيفية ساخنة',
                'subtitle_en' => 'Cool products for hot days',
                'subtitle_ar' => 'منتجات رائعة لأيام حارة',
                'start_date' => now()->addDays(120),
                'end_date' => now()->addDays(180),
                'products_count' => 5,
            ],
            [
                'name_en' => 'Back to School',
                'name_ar' => 'العودة إلى المدرسة',
                'title_en' => 'School Essentials',
                'title_ar' => 'ضروريات المدرسة',
                'subtitle_en' => 'Everything for a successful school year',
                'subtitle_ar' => 'كل ما تحتاجه لسنة دراسية ناجحة',
                'start_date' => now()->addDays(200),
                'end_date' => now()->addDays(240),
                'products_count' => 4,
            ],
            [
                'name_en' => 'Black Friday',
                'name_ar' => 'الجمعة السوداء',
                'title_en' => 'Biggest Sale of the Year',
                'title_ar' => 'أكبر تخفيض في السنة',
                'subtitle_en' => 'Unbeatable prices on everything',
                'subtitle_ar' => 'أسعار لا تقبل المنافسة على كل شيء',
                'start_date' => now()->addDays(300),
                'end_date' => now()->addDays(310),
                'products_count' => 6,
            ],
            [
                'name_en' => 'Holiday Season',
                'name_ar' => 'موسم العطلات',
                'title_en' => 'Festive Celebrations',
                'title_ar' => 'احتفالات احتفالية',
                'subtitle_en' => 'Gifts and decorations for the holidays',
                'subtitle_ar' => 'هدايا وديكورات للعطلات',
                'start_date' => now()->addDays(330),
                'end_date' => now()->addDays(365),
                'products_count' => 5,
            ],
            [
                'name_en' => 'Mother\'s Day',
                'name_ar' => 'عيد الأم',
                'title_en' => 'Celebrate Mom',
                'title_ar' => 'احتفل بالأم',
                'subtitle_en' => 'Special gifts for special moms',
                'subtitle_ar' => 'هدايا خاصة للأمهات الخاصات',
                'start_date' => now()->addDays(90),
                'end_date' => now()->addDays(105),
                'products_count' => 3,
            ],
            [
                'name_en' => 'Tech Gadgets Week',
                'name_ar' => 'أسبوع أدوات التكنولوجيا',
                'title_en' => 'Latest Tech Innovations',
                'title_ar' => 'أحدث الابتكارات التكنولوجية',
                'subtitle_en' => 'Cutting-edge technology products',
                'subtitle_ar' => 'منتجات تكنولوجية متقدمة',
                'start_date' => now()->addDays(150),
                'end_date' => now()->addDays(165),
                'products_count' => 4,
            ],
        ];

        $createdCount = 0;
        foreach ($occasions as $occasionData) {
            $occasion = Occasion::create([
                'vendor_id' => $this->vendor->id,
                'country_id' => $this->vendor->country_id,
                'slug' => Str::slug($occasionData['name_en']),
                'start_date' => $occasionData['start_date'],
                'end_date' => $occasionData['end_date'],
                'is_active' => true,
            ]);

            // Add translations
            $occasion->translations()->create([
                'lang_id' => $this->englishLangId,
                'lang_key' => 'name',
                'lang_value' => $occasionData['name_en'],
            ]);

            $occasion->translations()->create([
                'lang_id' => $this->arabicLangId,
                'lang_key' => 'name',
                'lang_value' => $occasionData['name_ar'],
            ]);

            $occasion->translations()->create([
                'lang_id' => $this->englishLangId,
                'lang_key' => 'title',
                'lang_value' => $occasionData['title_en'],
            ]);

            $occasion->translations()->create([
                'lang_id' => $this->arabicLangId,
                'lang_key' => 'title',
                'lang_value' => $occasionData['title_ar'],
            ]);

            $occasion->translations()->create([
                'lang_id' => $this->englishLangId,
                'lang_key' => 'sub_title',
                'lang_value' => $occasionData['subtitle_en'],
            ]);

            $occasion->translations()->create([
                'lang_id' => $this->arabicLangId,
                'lang_key' => 'sub_title',
                'lang_value' => $occasionData['subtitle_ar'],
            ]);

            // Add occasion products with special prices
            $selectedVariants = $variants->random(min($occasionData['products_count'], $variants->count()));
            $position = 0;

            foreach ($selectedVariants as $variant) {
                $basePrice = $variant->price ?? 100;
                $specialPrice = $basePrice * 0.85; // 15% discount

                OccasionProduct::create([
                    'occasion_id' => $occasion->id,
                    'vendor_product_id' => $variant->vendorProduct->id,
                    'vendor_product_variant_id' => $variant->id,
                    'special_price' => $specialPrice,
                    'position' => $position,
                ]);

                $position++;
            }

            $createdCount++;
            $this->command->info("✓ Created occasion: {$occasionData['name_en']} ({$occasionData['products_count']} products)");
        }

        $this->command->info("✓ Created {$createdCount} occasions");
    }
}
