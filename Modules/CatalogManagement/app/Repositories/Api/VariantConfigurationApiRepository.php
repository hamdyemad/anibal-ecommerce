<?php

namespace Modules\CatalogManagement\app\Repositories\Api;

use Modules\CatalogManagement\app\Interfaces\Api\VariantConfigurationApiRepositoryInterface;
use Modules\CatalogManagement\app\Models\VariantsConfiguration;
use Modules\CatalogManagement\app\Models\VariantConfigurationKey;

class VariantConfigurationApiRepository implements VariantConfigurationApiRepositoryInterface
{
    /**
     * Get all variant configuration keys
     */
    public function getAllKeys()
    {
        return VariantConfigurationKey::with('translations')->get();
    }

    /**
     * Find variant configuration key by ID
     */
    public function findKeyById(int $id)
    {
        return VariantConfigurationKey::with('translations')->find($id);
    }

    /**
     * Find variant configuration by ID with relationships
     */
    public function findById(int $id)
    {
        return VariantsConfiguration::with([
            'translations',
            'key.translations',
            'parent_data.translations',
            'children.translations',
            'children.children'
        ])->find($id);
    }

    /**
     * Get variants by key ID with optional parent filter
     */
    public function getVariantsByKeyId(int $keyId, ?int $parentId = null)
    {
        $query = VariantsConfiguration::with(['translations', 'children'])
            ->where('key_id', $keyId);

        if ($parentId !== null) {
            $query->where('parent_id', $parentId);
        } else {
            $query->whereNull('parent_id');
        }

        return $query->get();
    }

    /**
     * Get children of a variant
     */
    public function getChildrenById(int $id)
    {
        $variant = VariantsConfiguration::with(['children.translations', 'children.children'])->find($id);
        
        return $variant ? $variant->children : collect();
    }

    /**
     * Get selected path from root to variant
     */
    public function getSelectedPath(int $id): array
    {
        $variant = VariantsConfiguration::with('parent_data')->find($id);
        
        if (!$variant) {
            return [];
        }

        $path = [];
        $current = $variant;

        while ($current) {
            array_unshift($path, $current->id);
            $current = $current->parent_data;
        }

        return $path;
    }

    /**
     * Get variants at a specific level (by key and parent)
     */
    public function getVariantsAtLevel(int $keyId, ?int $parentId = null)
    {
        $query = VariantsConfiguration::with(['translations', 'children'])
            ->where('key_id', $keyId);

        if ($parentId === null) {
            $query->whereNull('parent_id');
        } else {
            $query->where('parent_id', $parentId);
        }

        return $query->get();
    }
}
