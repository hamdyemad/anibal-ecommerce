<?php

namespace Modules\CatalogManagement\app\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\CatalogManagement\app\Interfaces\VariantsConfigurationRepositoryInterface;
use Modules\CatalogManagement\app\Models\VariantsConfiguration;

class VariantsConfigurationRepository implements VariantsConfigurationRepositoryInterface
{
    /**
     * Get all variants configurations with relationships
     */
    public function getAll()
    {
        return VariantsConfiguration::with(['key', 'parent_data', 'children'])
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Get all variants configurations with pagination
     */
    public function getAllPaginated(array $filters = [], int $perPage = 20)
    {
        $query = VariantsConfiguration::with(['key.translations', 'translations'])
            ->orderBy('id', 'desc');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('translations', function ($tq) use ($search) {
                    $tq->where('lang_value', 'like', "%{$search}%");
                })->orWhere('value', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get variants configurations query for DataTables
     */
    public function getVariantsConfigurationsQuery(array $filters = [])
    {
        return VariantsConfiguration::with(['key', 'parent_data', 'children'])
            ->orderBy('id', 'desc');
    }

    /**
     * Find variants configuration by ID
     */
    public function findById(int $id)
    {
        return VariantsConfiguration::with([
            'translations',
            'key.translations',
            'parent_data',
            'children',
            'childrenRecursive.translations',
            'linkedChildren.translations',
            'linkedChildren.key'
        ])->find($id);
    }

    /**
     * Create a new variants configuration
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            \Log::info('VariantsConfiguration Create Request', [
                'data' => $data,
                'value' => $data['value'] ?? 'NOT SET',
                'type' => $data['type'] ?? 'NOT SET'
            ]);
            
            // Create the variant configuration
            $variantConfig = VariantsConfiguration::create([
                'key_id' => $data['key_id'] ?? null,
                'parent_id' => $data['parent_id'] ?? null,
                'value' => $data['value'] ?? null,
                'type' => $data['type'] ?? null,
            ]);

            // Set translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name']) && !empty($translation['name'])) {
                        $variantConfig->translations()->updateOrCreate([
                            'lang_id' => (int) $langId,
                            'lang_key' => 'name',
                        ], [
                            'lang_value' => $translation['name'],
                        ]);
                    }
                }
            }

            // Load relationships for return
            return $variantConfig->load(['translations', 'key', 'parent_data']);
        });
    }

    /**
     * Update variants configuration
     */
    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            \Log::info('VariantsConfiguration Update Request', [
                'id' => $id,
                'data' => $data
            ]);
            $variant = VariantsConfiguration::findOrFail($id);
            $variant->update([
                'key_id' => $data['key_id'] ?? null,
                'parent_id' => $data['parent_id'] ?? null,
                'value' => $data['value'] ?? null,
                'type' => $data['type'] ?? null,
            ]);

            // Set translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name']) && !empty($translation['name'])) {
                        $variant->translations()->updateOrCreate([
                            'lang_id' => (int) $langId,
                            'lang_key' => 'name',
                        ], [
                            'lang_value' => $translation['name'],
                        ]);
                    }
                }
            }
            return $variant;
        });
    }

    /**
     * Delete variants configuration
     */
    public function delete(int $id)
    {
        return DB::transaction(function () use ($id) {
            $variantKey = VariantsConfiguration::findOrFail($id);
            $variantKey->translations()->delete();
            $variantKey->delete();
            return $variantKey;
        });
    }

    /**
     * Get parent variants by key ID
     *
     * @param int $keyId
     * @param int|null $currentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getParentsByKey($keyId, $currentId = null)
    {
        $query = VariantsConfiguration::with(['key', 'parent_data.translations', 'translations'])
            ->where('key_id', $keyId);

        // Exclude current variant to prevent self-referencing
        if ($currentId) {
            $query->where('id', '!=', $currentId);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    /**
     * Get variants by key ID and parent ID (for hierarchical selection)
     *
     * @param int $keyId
     * @param int|null $parentId
     * @param int|null $currentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVariantsByKeyAndParent($keyId, $parentId = null, $currentId = null)
    {
        $query = VariantsConfiguration::with(['key', 'parent_data.translations', 'translations'])
            ->where('key_id', $keyId);

        // Filter by parent_id (null for root level variants)
        if ($parentId === null) {
            $query->whereNull('parent_id');
        } else {
            $query->where('parent_id', $parentId);
        }

        // Exclude current variant to prevent self-referencing
        if ($currentId) {
            $query->where('id', '!=', $currentId);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    /**
     * Get variant configuration keys for API
     *
     * @return array
     */
    public function getVariantKeysForApi()
    {
        $keys = \Modules\CatalogManagement\app\Models\VariantConfigurationKey::withoutGlobalScopes()
            ->with('translations')
            ->get();

        \Log::info('Found keys count: ' . $keys->count());

        return $keys->map(function ($key) {
            return [
                'id' => $key->id,
                'name' => $key->getTranslation('name', app()->getLocale()) ?? 'No Name',
            ];
        })->toArray();
    }

    /**
     * Get variants by key ID for API
     *
     * @param int $keyId
     * @param string|null $parentId
     * @return array
     */
    public function getVariantsByKeyForApi($keyId, $parentId = null)
    {
        // If parent_id is provided, get all children (direct + linked) of that parent
        if ($parentId && $parentId !== 'root') {
            // Get the parent variant
            $parent = VariantsConfiguration::withoutGlobalScopes()->find($parentId);
            
            if (!$parent) {
                return [];
            }
            
            // Get direct children (via parent_id)
            $directChildren = VariantsConfiguration::withoutGlobalScopes()
                ->with(['translations', 'children.translations', 'linkedChildren.translations', 'key.translations'])
                ->where('parent_id', $parentId)
                ->get();
            
            // Get linked children (via configuration_links table)
            $linkedChildren = $parent->linkedChildren()->with(['translations', 'children.translations', 'linkedChildren.translations', 'key.translations'])->get();
            
            // Merge and remove duplicates
            $allChildren = $directChildren->merge($linkedChildren)->unique('id');
            
            return $allChildren->map(function ($variant) {
                // For each child, check if it has children (direct or linked)
                $childDirectChildren = $variant->children ?? collect();
                $childLinkedChildren = $variant->linkedChildren ?? collect();
                $childAllChildren = $childDirectChildren->merge($childLinkedChildren)->unique('id');
                
                return [
                    'id' => $variant->id,
                    'name' => $variant->getTranslation('name', app()->getLocale()) ?? $variant->value,
                    'value' => $variant->value,
                    'key_id' => $variant->key_id,
                    'key_name' => $variant->key ? $variant->key->getTranslation('name', app()->getLocale()) : null,
                    'has_children' => $childAllChildren->count() > 0,
                    'children_count' => $childAllChildren->count()
                ];
            })->toArray();
        }
        
        // If parent_id is null or 'root', get root variants (no parent)
        $query = VariantsConfiguration::withoutGlobalScopes()
            ->with(['translations', 'children.translations', 'linkedChildren.translations', 'key.translations'])
            ->where('key_id', $keyId)
            ->whereNull('parent_id');

        $variants = $query->get();

        return $variants->map(function ($variant) {
            // Merge direct children and linked children
            $directChildren = $variant->children ?? collect();
            $linkedChildren = $variant->linkedChildren ?? collect();
            $allChildren = $directChildren->merge($linkedChildren)->unique('id');
            
            return [
                'id' => $variant->id,
                'name' => $variant->getTranslation('name', app()->getLocale()) ?? $variant->value,
                'value' => $variant->value,
                'key_id' => $variant->key_id,
                'key_name' => $variant->key ? $variant->key->getTranslation('name', app()->getLocale()) : null,
                'has_children' => $allChildren->count() > 0,
                'children_count' => $allChildren->count()
            ];
        })->toArray();
    }

    /**
     * Get variants configuration by key ID (only root level - parent_id is null)
     *
     * @param int $keyId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVariantsByKey($keyId)
    {
        return VariantsConfiguration::with('translations')
            ->where('key_id', $keyId)
            ->whereNull('parent_id')
            ->get();
    }

    /**
     * Get variant children recursively
     *
     * @param int $parentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVariantChildren($parentId)
    {
        // Get the parent variant to access linked children
        $parent = VariantsConfiguration::withoutGlobalScopes()->find($parentId);
        
        if (!$parent) {
            return collect();
        }
        
        // Get direct children (via parent_id)
        $directChildren = VariantsConfiguration::withoutGlobalScopes()
            ->with('translations')
            ->where('parent_id', $parentId)
            ->get();
        
        // Get linked children (via configuration_links table)
        $linkedChildren = $parent->linkedChildren()->with('translations')->get();
        
        // Merge and remove duplicates
        return $directChildren->merge($linkedChildren)->unique('id');
    }

    /**
     * Link a child configuration to a parent configuration
     *
     * @param int $parentId
     * @param int $childId
     * @return bool
     */
    public function linkConfiguration($parentId, $childId)
    {
        $parent = VariantsConfiguration::findOrFail($parentId);
        
        // Check if link already exists
        if (!$parent->linkedChildren()->where('child_config_id', $childId)->exists()) {
            $parent->linkedChildren()->attach($childId);
            return true;
        }
        
        return false;
    }

    /**
     * Unlink a child configuration from a parent configuration
     *
     * @param int $parentId
     * @param int $childId
     * @return bool
     */
    public function unlinkConfiguration($parentId, $childId)
    {
        $parent = VariantsConfiguration::findOrFail($parentId);
        $parent->linkedChildren()->detach($childId);
        return true;
    }

    /**
     * Sync linked children for a parent configuration
     *
     * @param int $parentId
     * @param array $childIds
     * @return array
     */
    public function syncLinkedChildren($parentId, array $childIds)
    {
        $parent = VariantsConfiguration::findOrFail($parentId);
        
        // Get current linked children to compare
        $currentChildIds = $parent->linkedChildren()->pluck('child_config_id')->toArray();
        
        // Calculate which children to add and which to remove
        $childIdsToAdd = array_diff($childIds, $currentChildIds);
        $childIdsToRemove = array_diff($currentChildIds, $childIds);
        
        // Remove old links
        if (!empty($childIdsToRemove)) {
            DB::table('variants_configurations_links')
                ->where('parent_config_id', $parentId)
                ->whereIn('child_config_id', $childIdsToRemove)
                ->delete();
        }
        
        // Add new links with calculated paths
        foreach ($childIdsToAdd as $childId) {
            $this->createLinkWithPath($parentId, $childId);
        }
        
        // Return sync result in Laravel's expected format
        return [
            'attached' => $childIdsToAdd,
            'detached' => $childIdsToRemove,
            'updated' => []
        ];
    }
    
    /**
     * Create a link with calculated hierarchy path
     */
    protected function createLinkWithPath($parentId, $childId)
    {
        // Calculate the complete hierarchy path
        $path = $this->calculateHierarchyPath($parentId, $childId);
        
        // Create the link with path
        DB::table('variants_configurations_links')->insert([
            'parent_config_id' => $parentId,
            'child_config_id' => $childId,
            'path' => json_encode($path),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        \Log::info('Created variant configuration link with path', [
            'parent_id' => $parentId,
            'child_id' => $childId,
            'path' => $path
        ]);
    }
    
    /**
     * Calculate the complete hierarchy path from root to child
     */
    protected function calculateHierarchyPath($parentId, $childId)
    {
        $path = [];
        
        // Build path from parent to root (backwards)
        $currentId = $parentId;
        $visited = []; // Prevent infinite loops
        
        while ($currentId && !in_array($currentId, $visited)) {
            $visited[] = $currentId;
            array_unshift($path, $currentId); // Add to beginning of array
            
            // Find parent of current node (either direct parent or linked parent)
            $current = VariantsConfiguration::find($currentId);
            if (!$current) break;
            
            // Check for direct parent first
            if ($current->parent_id) {
                $currentId = $current->parent_id;
            } else {
                // Check for linked parent
                $linkedParent = $current->linkedParents()->first();
                $currentId = $linkedParent ? $linkedParent->id : null;
            }
        }
        
        // Add the child to the end of the path
        $path[] = $childId;
        
        return $path;
    }

    /**
     * Get linked children for a parent configuration
     *
     * @param int $parentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLinkedChildren($parentId)
    {
        $parent = VariantsConfiguration::with(['linkedChildren.translations', 'linkedChildren.key'])
            ->findOrFail($parentId);
        
        return $parent->linkedChildren;
    }

    /**
     * Get all children (both direct and linked) for a parent configuration
     *
     * @param int $parentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllChildren($parentId)
    {
        $parent = VariantsConfiguration::with([
            'children.translations',
            'children.key',
            'linkedChildren.translations',
            'linkedChildren.key'
        ])->findOrFail($parentId);
        
        // Merge direct children and linked children, remove duplicates
        return $parent->children->merge($parent->linkedChildren)->unique('id');
    }
}
