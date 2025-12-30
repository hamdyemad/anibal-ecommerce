<?php

namespace Modules\CatalogManagement\app\Interfaces\Api;

interface VariantConfigurationApiRepositoryInterface
{
    /**
     * Get all variant configuration keys
     */
    public function getAllKeys();

    /**
     * Find variant configuration key by ID
     */
    public function findKeyById(int $id);

    /**
     * Find variant configuration by ID with relationships
     */
    public function findById(int $id);

    /**
     * Get variants by key ID with optional parent filter
     */
    public function getVariantsByKeyId(int $keyId, ?int $parentId = null);

    /**
     * Get children of a variant
     */
    public function getChildrenById(int $id);

    /**
     * Get selected path from root to variant
     */
    public function getSelectedPath(int $id): array;

    /**
     * Get variants at a specific level (by key and parent)
     */
    public function getVariantsAtLevel(int $keyId, ?int $parentId = null);
}
