<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permession;
use App\Models\Language;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get languages
        $languages = Language::whereIn('code', ['en', 'ar'])->get()->keyBy('code');

        // Define roles with translations
        $rolesData = [
            [
                'name' => 'super_admin',
                'translations' => [
                    'name' => ['en' => 'Super Admin', 'ar' => 'مدير عام'],
                ],
                'permissions' => 'all' // Will assign all permissions
            ],
            [
                'name' => 'admin',
                'translations' => [
                    'name' => ['en' => 'Admin', 'ar' => 'مشرف'],
                ],
                'permissions' => [] // Specific permissions can be added
            ],
            [
                'name' => 'vendor',
                'translations' => [
                    'name' => ['en' => 'Vendor', 'ar' => 'مورد'],
                ],
                'permissions' => [] // Specific permissions can be added
            ],
            [
                'name' => 'vendor_user',
                'translations' => [
                    'name' => ['en' => 'Vendor User', 'ar' => 'مستخدم مورد'],
                ],
                'permissions' => [] // Specific permissions can be added
            ],
        ];

        foreach ($rolesData as $roleData) {
            // Create or update the role
            $role = Role::updateOrCreate([]);

            // Add translations if available and languages exist
            if ($languages->isNotEmpty() && isset($roleData['translations'])) {
                foreach ($roleData['translations']['name'] as $locale => $value) {
                    $role->setTranslation('name', $value, $locale);
                }
            }

            // Assign permissions
            if ($roleData['permissions'] === 'all') {
                // Assign all permissions to super admin
                $allPermissions = Permession::all();
                $role->permessions()->sync($allPermissions->pluck('id'));
            } elseif (is_array($roleData['permissions']) && count($roleData['permissions']) > 0) {
                // Assign specific permissions
                $permissions = Permession::whereIn('key', $roleData['permissions'])->get();
                $role->permessions()->sync($permissions->pluck('id'));
            }
        }
    }
}
