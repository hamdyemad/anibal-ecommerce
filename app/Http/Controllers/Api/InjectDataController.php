<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\CategoryManagment\app\Models\Department;
use Modules\CategoryManagment\app\Models\Category;
use Modules\CategoryManagment\app\Models\SubCategory;
use Modules\CatalogManagement\app\Models\VariantConfigurationKey;
use Modules\CatalogManagement\app\Models\VariantsConfiguration;
use Modules\CatalogManagement\app\Models\Brand;
use Modules\CatalogManagement\app\Models\Tax;
use Modules\CatalogManagement\app\Models\Occasion;
use Modules\SystemSetting\app\Models\BlogCategory;
use Modules\Vendor\app\Models\Vendor;
use App\Models\User;
use App\Models\UserType;
use App\Models\Role;
use App\Models\Attachment;

class InjectDataController extends Controller
{
    protected string $sourceBaseUrl = 'https://bnaia.com';
    
    /**
     * Inject data from external API
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function inject(Request $request)
    {
        $include = $request->get('include', 'departments');
        
        try {
            $allData = [];
            $page = 1;
            $lastPage = 1;

            // Fetch all pages
            do {
                $response = Http::withOptions(['verify' => false])
                    ->timeout(30)
                    ->get("{$this->sourceBaseUrl}/api/inject-products", [
                        'include' => $include,
                        'page' => $page,
                    ]);

                if (!$response->successful()) {
                    return response()->json([
                        'status' => false,
                        'message' => "Failed to fetch page {$page} from source",
                    ], 500);
                }

                $data = $response->json();
                
                if (!isset($data['status']) || !$data['status']) {
                    return response()->json([
                        'status' => false,
                        'message' => $data['message'] ?? 'Source returned error',
                    ], 400);
                }

                // Handle categories special case (has main_categories and sub_categories)
                if ($include === 'categories') {
                    $mainCats = $data['data']['main_categories'] ?? null;
                    $subCats = $data['data']['sub_categories'] ?? null;
                    
                    if ($mainCats && isset($mainCats['data'])) {
                        $allData['main_categories'] = array_merge(
                            $allData['main_categories'] ?? [],
                            $mainCats['data']
                        );
                        $lastPage = max($lastPage, $mainCats['last_page'] ?? 1);
                    }
                    
                    if ($subCats && isset($subCats['data'])) {
                        $allData['sub_categories'] = array_merge(
                            $allData['sub_categories'] ?? [],
                            $subCats['data']
                        );
                        $lastPage = max($lastPage, $subCats['last_page'] ?? 1);
                    }
                } else {
                    // Get paginated data for other types
                    $paginatedData = $data['data'][$include] ?? null;
                    
                    if ($paginatedData && isset($paginatedData['data'])) {
                        $allData = array_merge($allData, $paginatedData['data']);
                        $lastPage = $paginatedData['last_page'] ?? 1;
                    }
                }

                $page++;

            } while ($page <= $lastPage);

            // Process and inject all data
            if ($include === 'categories') {
                $result = $this->injectData([
                    'main_categories' => ['data' => $allData['main_categories'] ?? []],
                    'sub_categories' => ['data' => $allData['sub_categories'] ?? []],
                ], $include);
                $totalFetched = count($allData['main_categories'] ?? []) + count($allData['sub_categories'] ?? []);
            } else {
                $result = $this->injectData([$include => ['data' => $allData]], $include);
                $totalFetched = count($allData);
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Data injected successfully',
                'total_fetched' => $totalFetched,
                'pages_fetched' => $lastPage,
                'injected' => $result,
            ]);

        } catch (\Exception $e) {
            Log::error('Inject data error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Inject data into database based on type
     */
    protected function injectData(array $data, string $include): array
    {
        return match ($include) {
            'departments' => $this->injectDepartments($data),
            'categories' => $this->injectAllCategories($data),
            'variant_keys' => $this->injectVariantKeys($data),
            'variants' => $this->injectVariants($data),
            'brands' => $this->injectBrands($data),
            'taxes' => $this->injectTaxes($data),
            'occasions' => $this->injectOccasions($data),
            'blog_categories' => $this->injectBlogCategories($data),
            default => ['message' => "Unknown include type: {$include}"],
        };
    }

    /**
     * Inject both main categories and sub categories
     */
    protected function injectAllCategories(array $data): array
    {
        $mainResult = $this->injectCategories($data);
        $subResult = $this->injectSubCategories($data);

        return [
            'main_categories' => $mainResult,
            'sub_categories' => $subResult,
        ];
    }

    /**
     * Inject departments into database
     */
    protected function injectDepartments(array $data): array
    {
        $items = $data['departments']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $errors = [];

        foreach ($items as $item) {
            try {
                DB::beginTransaction();

                // Check if department exists by ID (keep same ID from source)
                $department = Department::where('id', $item['id'])
                    ->first();

                if ($department) {
                    // Update existing
                    $department->update([
                        'slug' => $item['slug_en'],
                        'active' => $item['status'] == '1',
                        'created_at' => $this->parseDate($item['created_at'] ?? null),
                        'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                    ]);
                    $updated++;
                } else {
                    // Create new with same ID from source
                    $department = new Department();
                    $department->id = $item['id'];
                    $department->slug = $item['slug_en'];
                    $department->active = $item['status'] == '1';
                    $department->created_at = $this->parseDate($item['created_at'] ?? null);
                    $department->updated_at = $this->parseDate($item['updated_at'] ?? null);
                    $department->save();
                    $injected++;
                }

                // Set translations
                $department->setTranslation('name', 'en', $item['title_en']);
                $department->setTranslation('name', 'ar', $item['title_ar']);
                $department->save();

                // Download and attach image
                if (!empty($item['image'])) {
                    $this->attachImage($department, $item['image'], 'image');
                }

                // Download and attach icon
                if (!empty($item['icon'])) {
                    $this->attachImage($department, $item['icon'], 'icon');
                }

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                $errors[] = "Department {$item['title_en']} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting department: " . $e->getMessage());
            }
        }

        return [
            'type' => 'departments',
            'injected' => $injected,
            'updated' => $updated,
            'errors' => $errors,
        ];
    }

    /**
     * Inject categories into database
     */
    protected function injectCategories(array $data): array
    {
        $items = $data['main_categories']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $errors = [];

        foreach ($items as $item) {
            try {
                DB::beginTransaction();

                // Check if category exists by ID
                $category = Category::where('id', $item['id'])
                    ->first();

                if ($category) {
                    // Update existing
                    $category->update([
                        'slug' => $item['slug_en'] ?? $item['slug'] ?? null,
                        'department_id' => $item['department_id'] ?? null,
                        'active' => ($item['status'] ?? '1') == '1',
                        'created_at' => $this->parseDate($item['created_at'] ?? null),
                        'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                    ]);
                    $updated++;
                } else {
                    // Create new with same ID
                    $category = new Category();
                    $category->id = $item['id'];
                    $category->slug = $item['slug_en'] ?? $item['slug'] ?? null;
                    $category->department_id = $item['department_id'] ?? null;
                    $category->active = ($item['status'] ?? '1') == '1';
                    $category->created_at = $this->parseDate($item['created_at'] ?? null);
                    $category->updated_at = $this->parseDate($item['updated_at'] ?? null);
                    $category->save();
                    $injected++;
                }

                // Set translations
                if (!empty($item['title_en'])) {
                    $category->setTranslation('name', 'en', $item['title_en']);
                }
                if (!empty($item['title_ar'])) {
                    $category->setTranslation('name', 'ar', $item['title_ar']);
                }
                $category->save();

                // Download and attach image
                if (!empty($item['image'])) {
                    $this->attachImage($category, $item['image'], 'image');
                }

                // Download and attach icon
                if (!empty($item['icon'])) {
                    $this->attachImage($category, $item['icon'], 'icon');
                }

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                $titleEn = $item['title_en'] ?? $item['id'];
                $errors[] = "Category {$titleEn} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting category: " . $e->getMessage());
            }
        }

        return [
            'type' => 'main_categories',
            'injected' => $injected,
            'updated' => $updated,
            'errors' => $errors,
        ];
    }

    /**
     * Inject sub-categories into database
     */
    protected function injectSubCategories(array $data): array
    {
        $items = $data['sub_categories']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($items as $item) {
            try {
                DB::beginTransaction();

                // Get category_id - try different possible keys
                $categoryId = $item['category_id'] ?? $item['main_category_id'] ?? $item['parent_id'] ?? null;
                
                // Skip if no category_id (can't create orphan subcategory)
                if (!$categoryId) {
                    $skipped++;
                    DB::rollBack();
                    continue;
                }

                // Check if subcategory exists by ID
                $subCategory = SubCategory::where('id', $item['id'])
                    ->first();

                if ($subCategory) {
                    // Update existing
                    $subCategory->update([
                        'slug' => $item['slug_en'] ?? $item['slug'] ?? null,
                        'category_id' => $categoryId,
                        'active' => ($item['status'] ?? '1') == '1',
                        'created_at' => $this->parseDate($item['created_at'] ?? null),
                        'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                    ]);
                    $updated++;
                } else {
                    // Create new with same ID
                    $subCategory = new SubCategory();
                    $subCategory->id = $item['id'];
                    $subCategory->slug = $item['slug_en'] ?? $item['slug'] ?? null;
                    $subCategory->category_id = $categoryId;
                    $subCategory->active = ($item['status'] ?? '1') == '1';
                    $subCategory->created_at = $this->parseDate($item['created_at'] ?? null);
                    $subCategory->updated_at = $this->parseDate($item['updated_at'] ?? null);
                    $subCategory->save();
                    $injected++;
                }

                // Set translations
                if (!empty($item['title_en'])) {
                    $subCategory->setTranslation('name', 'en', $item['title_en']);
                }
                if (!empty($item['title_ar'])) {
                    $subCategory->setTranslation('name', 'ar', $item['title_ar']);
                }
                $subCategory->save();

                // Download and attach image
                if (!empty($item['image'])) {
                    $this->attachImage($subCategory, $item['image'], 'image');
                }

                // Download and attach icon
                if (!empty($item['icon'])) {
                    $this->attachImage($subCategory, $item['icon'], 'icon');
                }

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                $titleEn = $item['title_en'] ?? $item['id'];
                $errors[] = "SubCategory {$titleEn} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting subcategory: " . $e->getMessage());
            }
        }

        return [
            'type' => 'sub_categories',
            'injected' => $injected,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Inject variant keys into database
     */
    protected function injectVariantKeys(array $data): array
    {
        $items = $data['variant_keys']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $errors = [];

        // First pass: create/update all keys without parent_key_id to avoid FK issues
        // Second pass: update parent_key_id relationships
        
        // Sort items: parent keys first (null parent_key_id), then children
        usort($items, function($a, $b) {
            $aParent = $a['parent_key_id'] ?? null;
            $bParent = $b['parent_key_id'] ?? null;
            if ($aParent === null && $bParent !== null) return -1;
            if ($aParent !== null && $bParent === null) return 1;
            return 0;
        });

        foreach ($items as $item) {
            try {
                DB::beginTransaction();

                // Check if variant key exists by ID
                $variantKey = VariantConfigurationKey::where('id', $item['id'])->first();

                if ($variantKey) {
                    // Update existing
                    $variantKey->update([
                        'parent_key_id' => $item['parent_key_id'] ?? null,
                        'created_at' => $this->parseDate($item['created_at'] ?? null),
                        'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                    ]);
                    $updated++;
                } else {
                    // Create new with same ID
                    $variantKey = new VariantConfigurationKey();
                    $variantKey->id = $item['id'];
                    $variantKey->parent_key_id = $item['parent_key_id'] ?? null;
                    $variantKey->created_at = $this->parseDate($item['created_at'] ?? null);
                    $variantKey->updated_at = $this->parseDate($item['updated_at'] ?? null);
                    $variantKey->save();
                    $injected++;
                }

                // Set translations
                if (!empty($item['name_en'])) {
                    $variantKey->setTranslation('name', 'en', $item['name_en']);
                }
                if (!empty($item['name_ar'])) {
                    $variantKey->setTranslation('name', 'ar', $item['name_ar']);
                }
                $variantKey->save();

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                $nameEn = $item['name_en'] ?? $item['id'];
                $errors[] = "VariantKey {$nameEn} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting variant key: " . $e->getMessage());
            }
        }

        return [
            'type' => 'variant_keys',
            'injected' => $injected,
            'updated' => $updated,
            'errors' => $errors,
        ];
    }

    /**
     * Inject variants into database
     */
    protected function injectVariants(array $data): array
    {
        $items = $data['variants']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        // Sort items: parent variants first (null parent_id), then children
        usort($items, function($a, $b) {
            $aParent = $a['parent_id'] ?? null;
            $bParent = $b['parent_id'] ?? null;
            if ($aParent === null && $bParent !== null) return -1;
            if ($aParent !== null && $bParent === null) return 1;
            return 0;
        });

        foreach ($items as $item) {
            // Skip empty items
            if (empty($item) || !isset($item['id'])) {
                $skipped++;
                continue;
            }

            try {
                DB::beginTransaction();

                // Check if variant exists by ID
                $variant = VariantsConfiguration::where('id', $item['id'])->first();

                if ($variant) {
                    // Update existing
                    $variant->update([
                        'key_id' => $item['key_id'] ?? null,
                        'parent_id' => $item['parent_id'] ?? null,
                        'value' => $item['color'] ?? null,
                        'type' => !empty($item['color']) ? 'color' : 'text',
                        'created_at' => $this->parseDate($item['created_at'] ?? null),
                        'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                    ]);
                    $updated++;
                } else {
                    // Create new with same ID
                    $variant = new VariantsConfiguration();
                    $variant->id = $item['id'];
                    $variant->key_id = $item['key_id'] ?? null;
                    $variant->parent_id = $item['parent_id'] ?? null;
                    $variant->value = $item['color'] ?? null;
                    $variant->type = !empty($item['color']) ? 'color' : 'text';
                    $variant->created_at = $this->parseDate($item['created_at'] ?? null);
                    $variant->updated_at = $this->parseDate($item['updated_at'] ?? null);
                    $variant->save();
                    $injected++;
                }

                // Set translations
                if (!empty($item['name_en'])) {
                    $variant->setTranslation('name', 'en', $item['name_en']);
                }
                if (!empty($item['name_ar'])) {
                    $variant->setTranslation('name', 'ar', $item['name_ar']);
                }
                $variant->save();

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                $nameEn = $item['name_en'] ?? $item['id'];
                $errors[] = "Variant {$nameEn} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting variant: " . $e->getMessage());
            }
        }

        return [
            'type' => 'variants',
            'injected' => $injected,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Inject brands into database (also creates vendor and user for each brand)
     */
    protected function injectBrands(array $data): array
    {
        $items = $data['brands']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $errors = [];

        foreach ($items as $item) {
            try {
                DB::beginTransaction();

                // Check if brand exists by ID
                $brand = Brand::where('id', $item['id'])->first();

                if ($brand) {
                    // Update existing brand
                    $brand->update([
                        'slug' => $item['slug_en'] ?? null,
                        'facebook_url' => $item['facebook'] ?? null,
                        'twitter_url' => $item['x'] ?? null,
                        'linkedin_url' => $item['linkedin'] ?? null,
                        'pinterest_url' => $item['pinterest'] ?? null,
                        'created_at' => $this->parseDate($item['created_at'] ?? null),
                        'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                    ]);
                    $updated++;
                } else {
                    // Create new brand with same ID
                    $brand = new Brand();
                    $brand->id = $item['id'];
                    $brand->slug = $item['slug_en'] ?? null;
                    $brand->facebook_url = $item['facebook'] ?? null;
                    $brand->twitter_url = $item['x'] ?? null;
                    $brand->linkedin_url = $item['linkedin'] ?? null;
                    $brand->pinterest_url = $item['pinterest'] ?? null;
                    $brand->active = true;
                    $brand->created_at = $this->parseDate($item['created_at'] ?? null);
                    $brand->updated_at = $this->parseDate($item['updated_at'] ?? null);
                    $brand->save();
                    $injected++;
                }

                // Set brand translations
                if (!empty($item['name_en'])) {
                    $brand->setTranslation('name', 'en', $item['name_en']);
                }
                if (!empty($item['name_ar'])) {
                    $brand->setTranslation('name', 'ar', $item['name_ar']);
                }
                if (!empty($item['describtion_en'])) {
                    $brand->setTranslation('description', 'en', $item['describtion_en']);
                }
                if (!empty($item['describtion_ar'])) {
                    $brand->setTranslation('description', 'ar', $item['describtion_ar']);
                }
                $brand->save();

                // Download and attach logo
                if (!empty($item['logo'])) {
                    $this->attachImage($brand, $item['logo'], 'logo');
                }

                // Download and attach cover
                if (!empty($item['cover'])) {
                    $this->attachImage($brand, $item['cover'], 'cover');
                }

                // Create or update vendor for this brand (same ID)
                $this->createOrUpdateVendorForBrand($item);

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                $nameEn = $item['name_en'] ?? $item['id'];
                $errors[] = "Brand {$nameEn} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting brand: " . $e->getMessage());
            }
        }

        return [
            'type' => 'brands',
            'injected' => $injected,
            'updated' => $updated,
            'errors' => $errors,
        ];
    }

    /**
     * Create or update vendor and user for a brand
     */
    protected function createOrUpdateVendorForBrand(array $item): void
    {
        // Check if vendor exists with same ID
        $vendor = Vendor::where('id', $item['id'])->first();
        
        // Generate email from slug
        $email = ($item['slug_en'] ?? 'vendor-' . $item['id']) . '@bnaia.com';

        if (!$vendor) {
            // Create user first
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                $user = new User();
                $user->uuid = Str::uuid();
                $user->email = $email;
                $user->password = bcrypt('password123');
                $user->user_type_id = UserType::VENDOR_TYPE;
                $user->active = true;
                $user->block = false;
                $user->save();

                // Set user name translations
                if (!empty($item['name_en'])) {
                    $user->setTranslation('name', 'en', $item['name_en']);
                }
                if (!empty($item['name_ar'])) {
                    $user->setTranslation('name', 'ar', $item['name_ar']);
                }
                $user->save();

                // Assign vendor role to user
                $vendorRole = Role::where('type', Role::VENDOR_ROLE_TYPE)->first();
                if ($vendorRole) {
                    $user->roles()->sync([$vendorRole->id]);
                }

                // Attach logo image to user
                if (!empty($item['logo'])) {
                    $localPath = $this->downloadImage($item['logo']);
                    if ($localPath) {
                        $user->update(['image' => $localPath]);
                    }
                }
            }

            // Create vendor with same ID as brand
            $vendor = new Vendor();
            $vendor->id = $item['id'];
            $vendor->user_id = $user->id;
            $vendor->slug = $item['slug_en'] ?? null;
            $vendor->active = true;
            $vendor->created_at = $this->parseDate($item['created_at'] ?? null);
            $vendor->updated_at = $this->parseDate($item['updated_at'] ?? null);
            $vendor->save();

            // Update user with vendor_id
            $user->update(['vendor_id' => $vendor->id]);
        } else {
            // Update existing vendor
            $vendor->update([
                'slug' => $item['slug_en'] ?? $vendor->slug,
                'created_at' => $this->parseDate($item['created_at'] ?? null),
                'updated_at' => $this->parseDate($item['updated_at'] ?? null),
            ]);

            // Update user image if vendor has a user
            if ($vendor->user && !empty($item['logo'])) {
                $localPath = $this->downloadImage($item['logo']);
                if ($localPath) {
                    $vendor->user->update(['image' => $localPath]);
                }
            }
        }

        // Set vendor translations
        if (!empty($item['name_en'])) {
            $vendor->setTranslation('name', 'en', $item['name_en']);
        }
        if (!empty($item['name_ar'])) {
            $vendor->setTranslation('name', 'ar', $item['name_ar']);
        }
        if (!empty($item['describtion_en'])) {
            $vendor->setTranslation('description', 'en', $item['describtion_en']);
        }
        if (!empty($item['describtion_ar'])) {
            $vendor->setTranslation('description', 'ar', $item['describtion_ar']);
        }
        $vendor->save();

        // Attach logo to vendor
        if (!empty($item['logo'])) {
            $this->attachImage($vendor, $item['logo'], 'logo');
        }

        // Attach cover as banner to vendor
        if (!empty($item['cover'])) {
            $this->attachImage($vendor, $item['cover'], 'banner');
        }
    }

    /**
     * Inject taxes into database
     */
    protected function injectTaxes(array $data): array
    {
        $items = $data['taxes']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($items as $item) {
            // Skip empty items
            if (empty($item) || !isset($item['id'])) {
                $skipped++;
                continue;
            }

            try {
                DB::beginTransaction();

                // Check if tax exists by ID
                $tax = Tax::where('id', $item['id'])->first();

                if ($tax) {
                    // Update existing
                    $tax->update([
                        'percentage' => $item['precentage'] ?? 0,
                        'is_active' => ($item['status'] ?? '0') == '1',
                        'created_at' => $item['created_at'] ? \Carbon\Carbon::parse($item['created_at']) : null,
                        'updated_at' => $item['updated_at'] ? \Carbon\Carbon::parse($item['updated_at']) : null,
                    ]);
                    $updated++;
                } else {
                    // Create new with same ID
                    $tax = new Tax();
                    $tax->id = $item['id'];
                    $tax->percentage = $item['precentage'] ?? 0;
                    $tax->is_active = ($item['status'] ?? '0') == '1';
                    $tax->created_at = $item['created_at'] ? \Carbon\Carbon::parse($item['created_at']) : null;
                    $tax->updated_at = $item['updated_at'] ? \Carbon\Carbon::parse($item['updated_at']) : null;
                    $tax->save();
                    $injected++;
                }

                // Set translations
                if (!empty($item['title_en'])) {
                    $tax->setTranslation('name', 'en', $item['title_en']);
                }
                if (!empty($item['title_ar'])) {
                    $tax->setTranslation('name', 'ar', $item['title_ar']);
                }
                $tax->save();

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                $titleEn = $item['title_en'] ?? $item['id'];
                $errors[] = "Tax {$titleEn} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting tax: " . $e->getMessage());
            }
        }

        return [
            'type' => 'taxes',
            'injected' => $injected,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Inject occasions into database
     */
    protected function injectOccasions(array $data): array
    {
        $items = $data['occasions']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($items as $item) {
            // Skip empty items
            if (empty($item) || !isset($item['id'])) {
                $skipped++;
                continue;
            }

            try {
                DB::beginTransaction();

                // Check if occasion exists by ID
                $occasion = Occasion::where('id', $item['id'])->first();

                // Parse dates
                $startDate = $this->parseDate($item['start_date'] ?? null);
                $endDate = $this->parseDate($item['end_date'] ?? null);

                if ($occasion) {
                    // Update existing
                    $occasion->update([
                        'slug' => $item['slug_en'] ?? null,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'is_active' => ($item['status'] ?? '0') == '1',
                        'show_in_mobile' => $item['show_in_mobile'] ?? false,
                        'created_at' => $this->parseDate($item['created_at'] ?? null),
                        'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                    ]);
                    $updated++;
                } else {
                    // Create new with same ID
                    $occasion = new Occasion();
                    $occasion->id = $item['id'];
                    $occasion->slug = $item['slug_en'] ?? null;
                    $occasion->start_date = $startDate;
                    $occasion->end_date = $endDate;
                    $occasion->is_active = ($item['status'] ?? '0') == '1';
                    $occasion->show_in_mobile = $item['show_in_mobile'] ?? false;
                    $occasion->created_at = $this->parseDate($item['created_at'] ?? null);
                    $occasion->updated_at = $this->parseDate($item['updated_at'] ?? null);
                    $occasion->save();
                    $injected++;
                }

                // Set translations
                if (!empty($item['name_en'])) {
                    $occasion->setTranslation('name', 'en', $item['name_en']);
                }
                if (!empty($item['name_ar'])) {
                    $occasion->setTranslation('name', 'ar', $item['name_ar']);
                }
                if (!empty($item['title_en'])) {
                    $occasion->setTranslation('title', 'en', $item['title_en']);
                }
                if (!empty($item['title_ar'])) {
                    $occasion->setTranslation('title', 'ar', $item['title_ar']);
                }
                if (!empty($item['subtitle_en'])) {
                    $occasion->setTranslation('subtitle', 'en', $item['subtitle_en']);
                }
                if (!empty($item['subtitle_ar'])) {
                    $occasion->setTranslation('subtitle', 'ar', $item['subtitle_ar']);
                }
                if (!empty($item['description_en'])) {
                    $occasion->setTranslation('description', 'en', $item['description_en']);
                }
                if (!empty($item['description_ar'])) {
                    $occasion->setTranslation('description', 'ar', $item['description_ar']);
                }
                $occasion->save();

                // Download and attach image
                if (!empty($item['image'])) {
                    $this->attachImage($occasion, $item['image'], 'image');
                }

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                $nameEn = $item['name_en'] ?? $item['id'];
                $errors[] = "Occasion {$nameEn} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting occasion: " . $e->getMessage());
            }
        }

        return [
            'type' => 'occasions',
            'injected' => $injected,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Inject blog categories into database
     */
    protected function injectBlogCategories(array $data): array
    {
        $items = $data['blog_categories']['data'] ?? [];
        $injected = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($items as $item) {
            // Skip empty items
            if (empty($item) || !isset($item['id'])) {
                $skipped++;
                continue;
            }

            try {
                DB::beginTransaction();

                // Check if blog category exists by ID
                $blogCategory = BlogCategory::where('id', $item['id'])->first();

                if ($blogCategory) {
                    // Update existing
                    $blogCategory->update([
                        'slug' => $item['slug_en'] ?? null,
                        'active' => ($item['status'] ?? '0') == '1',
                        'created_at' => $this->parseDate($item['created_at'] ?? null),
                        'updated_at' => $this->parseDate($item['updated_at'] ?? null),
                    ]);
                    $updated++;
                } else {
                    // Create new with same ID
                    $blogCategory = new BlogCategory();
                    $blogCategory->id = $item['id'];
                    $blogCategory->slug = $item['slug_en'] ?? null;
                    $blogCategory->active = ($item['status'] ?? '0') == '1';
                    $blogCategory->created_at = $this->parseDate($item['created_at'] ?? null);
                    $blogCategory->updated_at = $this->parseDate($item['updated_at'] ?? null);
                    $blogCategory->save();
                    $injected++;
                }

                // Set translations
                if (!empty($item['title_en'])) {
                    $blogCategory->setTranslation('title', 'en', $item['title_en']);
                }
                if (!empty($item['title_ar'])) {
                    $blogCategory->setTranslation('title', 'ar', $item['title_ar']);
                }
                if (!empty($item['description_en'])) {
                    $blogCategory->setTranslation('description', 'en', $item['description_en']);
                }
                if (!empty($item['description_ar'])) {
                    $blogCategory->setTranslation('description', 'ar', $item['description_ar']);
                }
                $blogCategory->save();

                // Download and attach image
                if (!empty($item['image'])) {
                    $this->attachImage($blogCategory, $item['image'], 'image');
                }

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                $titleEn = $item['title_en'] ?? $item['id'];
                $errors[] = "BlogCategory {$titleEn} (ID: {$item['id']}): " . $e->getMessage();
                Log::error("Error injecting blog category: " . $e->getMessage());
            }
        }

        return [
            'type' => 'blog_categories',
            'injected' => $injected,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }
    /**
     * Download image and attach to model
     */
    protected function attachImage($model, string $imagePath, string $type): void
    {
        try {
            // Download image first
            $localPath = $this->downloadImage($imagePath);
            
            if (!$localPath) {
                return;
            }

            // Check if attachment already exists
            $existingAttachment = $model->attachments()->where('type', $type)->first();
            
            if ($existingAttachment) {
                // Update existing attachment
                $existingAttachment->update(['path' => $localPath]);
            } else {
                // Create new attachment
                Attachment::create([
                    'attachable_type' => get_class($model),
                    'attachable_id' => $model->id,
                    'path' => $localPath,
                    'type' => $type,
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Error attaching image {$imagePath}: " . $e->getMessage());
        }
    }

    /**
     * Download image and save locally with same path
     */
    protected function downloadImage(string $imagePath): ?string
    {
        try {
            if (empty($imagePath)) {
                return null;
            }

            // Build full URL
            $imageUrl = "{$this->sourceBaseUrl}/storage/{$imagePath}";

            // Check if file already exists locally
            if (Storage::disk('public')->exists($imagePath)) {
                Log::info("Image already exists: {$imagePath}");
                return $imagePath;
            }

            // Download image
            $response = Http::withOptions(['verify' => false])
                ->timeout(15)
                ->get($imageUrl);
            
            if (!$response->successful()) {
                Log::warning("Failed to download image: {$imageUrl} - Status: {$response->status()}");
                return null;
            }

            // Ensure directory exists
            $directory = dirname($imagePath);
            if ($directory && $directory !== '.') {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Save image locally with same path
            Storage::disk('public')->put($imagePath, $response->body());
            
            Log::info("Image downloaded: {$imagePath}");
            return $imagePath;

        } catch (\Exception $e) {
            Log::error("Error downloading image {$imagePath}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Parse date string from API format to Carbon instance
     * Handles format like "21 Oct, 2025, 04:19 PM"
     */
    protected function parseDate(?string $dateString): ?\Carbon\Carbon
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            // Try parsing the API format: "21 Oct, 2025, 04:19 PM"
            return \Carbon\Carbon::createFromFormat('d M, Y, h:i A', $dateString);
        } catch (\Exception $e) {
            try {
                // Fallback: try standard parsing
                return \Carbon\Carbon::parse($dateString);
            } catch (\Exception $e) {
                return null;
            }
        }
    }
}
