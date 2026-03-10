<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Models\Translation;

class TruncateController extends Controller
{
    protected array $tableGroups = [
        'departments' => [
            'tables' => ['departments'],
            'folders' => ['department-images'],
            'attachable_type' => 'Modules\\CategoryManagment\\app\\Models\\Department',
        ],
        'categories' => [
            'tables' => ['categories'],
            'folders' => ['category-images'],
            'attachable_type' => 'Modules\\CategoryManagment\\app\\Models\\Category',
        ],
        'sub_categories' => [
            'tables' => ['sub_categories'],
            'folders' => ['sub-category-images'],
            'attachable_type' => 'Modules\\CategoryManagment\\app\\Models\\SubCategory',
        ],
        'products' => [
            'tables' => ['products', 'product_variants'],
            'folders' => ['product-images'],
            'attachable_type' => 'Modules\\CatalogManagement\\app\\Models\\Product',
        ],
        'vendors' => [
            'tables' => ['vendors', 'vendor_products', 'vendor_product_variants', 'vendor_product_variant_stocks', 'vendor_requests'],
            'folders' => ['vendor-images'],
            'attachable_type' => 'Modules\\Vendor\\app\\Models\\Vendor',
            'delete_vendor_users' => true,
        ],
        'brands' => [
            'tables' => ['brands'],
            'folders' => ['brands-images'],
            'attachable_type' => 'Modules\\CatalogManagement\\app\\Models\\Brand',
        ],
        'orders' => [
            'tables' => ['orders', 'order_extra_fees_discounts', 'order_fulfillments', 'order_products', 'order_product_taxes'],
            'folders' => [],
            'attachable_type' => null,
        ],
        'customers' => [
            'tables' => ['customers', 'customer_addresses', 'customer_fcm_tokens', 'customer_otps', 'customer_password_reset_tokens'],
            'folders' => [],
            'attachable_type' => null,
        ],
        'areas' => [
            'tables' => ['cities', 'regions', 'subregions'],
            'folders' => [],
            'attachable_type' => null,
        ],
    ];

    public function truncate(Request $request)
    {
        if ($request->query('key') !== 'MY_SECRET_KEY_123') {
            abort(403, 'Unauthorized');
        }
        
        $only = $request->query('only');
        
        // If no 'only' param, return error - must specify what to truncate
        if (!$only) {
            return response()->json([
                'success' => false,
                'message' => 'Please specify which tables to truncate using ?only= parameter',
                'available_groups' => array_keys($this->tableGroups),
                'example' => '?key=MY_SECRET_KEY_123&only=departments,categories',
                'truncate_all' => '?key=MY_SECRET_KEY_123&only=all',
            ], 400);
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $truncatedTables = [];
        $deletedFiles = 0;
        $deletedFolders = 0;
        $deletedAttachments = 0;
        $deletedUsers = 0;
        
        // If 'all' is specified, truncate everything
        if ($only === 'all') {
            return $this->truncateAll();
        }
        
        // Truncate only specified groups
        $selectedGroups = array_map('trim', explode(',', $only));
        
        foreach ($selectedGroups as $groupName) {
            if (!isset($this->tableGroups[$groupName])) {
                continue;
            }
            
            $group = $this->tableGroups[$groupName];
            $result = $this->truncateGroup($group);
            
            $truncatedTables = array_merge($truncatedTables, $result['tables']);
            $deletedFiles += $result['files'];
            $deletedFolders += $result['folders'];
            $deletedAttachments += $result['attachments'];
            $deletedUsers += $result['users'] ?? 0;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        return response()->json([
            'success' => true,
            'message' => 'Selected tables truncated',
            'tables_truncated' => $truncatedTables,
            'tables_count' => count($truncatedTables),
            'folders_deleted' => $deletedFolders,
            'files_deleted' => $deletedFiles,
            'attachments_deleted' => $deletedAttachments,
            'users_deleted' => $deletedUsers,
        ]);
    }

    protected function truncateGroup(array $group): array
    {
        $truncatedTables = [];
        $deletedFiles = 0;
        $deletedFolders = 0;
        $deletedAttachments = 0;
        $deletedUsers = 0;

        // Delete vendor users if this is the vendors group
        if (!empty($group['delete_vendor_users'])) {
            // Get vendor user type IDs (VENDOR_TYPE = 3, VENDOR_USER_TYPE = 4)
            $vendorUserTypeIds = [3, 4];
            
            // Delete user_role entries for vendor users
            $vendorUserIds = DB::table('users')
                ->whereIn('user_type_id', $vendorUserTypeIds)
                ->pluck('id');
            
            if ($vendorUserIds->count() > 0) {
                DB::table('user_role')->whereIn('user_id', $vendorUserIds)->delete();
                
                // Delete translations for vendor users
                DB::table('translations')
                    ->where('translatable_type', 'App\\Models\\User')
                    ->whereIn('translatable_id', $vendorUserIds)
                    ->delete();
            }
            
            // Delete vendor users
            $deletedUsers = DB::table('users')
                ->whereIn('user_type_id', $vendorUserTypeIds)
                ->delete();
        }

        // Delete attachments for this type
        if ($group['attachable_type']) {
            $deletedAttachments = DB::table('attachments')
                ->where('attachable_type', $group['attachable_type'])
                ->count();
            DB::table('attachments')
                ->where('attachable_type', $group['attachable_type'])
                ->delete();
                
            // Delete translations for this type
            DB::table('translations')
                ->where('translatable_type', $group['attachable_type'])
                ->delete();
        }
        
        // Truncate tables
        foreach ($group['tables'] as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $truncatedTables[] = $table;
            }
        }
        
        // Delete storage folders
        foreach ($group['folders'] as $folder) {
            if (Storage::disk('public')->exists($folder)) {
                $files = Storage::disk('public')->allFiles($folder);
                $deletedFiles += count($files);
                Storage::disk('public')->deleteDirectory($folder);
                $deletedFolders++;
            }
        }
        
        return [
            'tables' => $truncatedTables,
            'files' => $deletedFiles,
            'folders' => $deletedFolders,
            'attachments' => $deletedAttachments,
            'users' => $deletedUsers,
        ];
    }

    protected function truncateAll()
    {
        Translation::query()->forceDelete();
        
        $tables = [
            'activity_logs', 'cities', 'regions', 'subregions',
            'attachments', 'brands', 'bundle_categories', 'categories',
            'departments', 'customers', 'customer_addresses', 'customer_fcm_tokens',
            'customer_otps', 'customer_password_reset_tokens',
            'orders', 'order_extra_fees_discounts', 'order_stages',
            'order_fulfillments', 'order_products', 'order_product_taxes',
            'products', 'product_variants', 'promocodes', 'reviews', 'sub_categories',
            'taxes', 'translations', 'variants_configurations', 'variants_configurations_keys',
            'vendors', 'vendor_products', 'vendor_product_variants',
            'vendor_product_variant_stocks', 'vendor_requests',
            'wishlists', 'withdraws'
        ];

        $truncatedTables = [];
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $truncatedTables[] = $table;
            }
        }
        
        // Clear all storage folders
        $foldersToClean = [
            'department-images',
            'category-images', 
            'sub-category-images',
            'product-images',
            'brands-images',
            'vendor-images',
            'banner-images',
            'attachments',
        ];
        
        $deletedFiles = 0;
        $deletedFolders = 0;
        
        foreach ($foldersToClean as $folder) {
            if (Storage::disk('public')->exists($folder)) {
                $files = Storage::disk('public')->allFiles($folder);
                $deletedFiles += count($files);
                Storage::disk('public')->deleteDirectory($folder);
                $deletedFolders++;
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        return response()->json([
            'success' => true,
            'message' => 'All tables truncated and storage cleaned',
            'tables_truncated' => $truncatedTables,
            'tables_count' => count($truncatedTables),
            'folders_deleted' => $deletedFolders,
            'files_deleted' => $deletedFiles,
        ]);
    }
}
