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

        Role::query()->delete();

        // Define roles with translations
        $rolesData = [
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
                    $role->setTranslation('name', $locale, $value);
                }
            }

            // Assign specific permissions
            $permissions = Permession::whereIn('key', $roleData['permissions'])->get();
            $role->permessions()->sync($permissions->pluck('id'));
        }
    }
}
