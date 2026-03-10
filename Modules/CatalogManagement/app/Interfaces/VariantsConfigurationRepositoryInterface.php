<?php

namespace Modules\CatalogManagement\app\Interfaces;

interface VariantsConfigurationRepositoryInterface
{
    /**
     * Get all variants configurations with relationships
     */
    public function getAll();

    /**
     * Get variants configurations query for DataTables
     */
    public function getVariantsConfigurationsQuery(array $filters = []);

    /**
     * Find variants configuration by ID
     */
    public function findById(int $id);

    /**
     * Create a new variants configuration
     */
    public function create(array $data);

    /**
     * Update variants configuration
     */
    public function update(int $id, array $data);

    /**
     * Delete variants configuration
     */
    public function delete(int $id);

    /**
     * Link a child configuration to a parent configuration
     */
    public function linkConfiguration($parentId, $childId);

    /**
     * Unlink a child configuration from a parent configuration
     */
    public function unlinkConfiguration($parentId, $childId);

    /**
     * Sync linked children for a parent configuration
     */
    public function syncLinkedChildren($parentId, array $childIds);

    /**
     * Get linked children for a parent configuration
     */
    public function getLinkedChildren($parentId);

    /**
     * Get all children (both direct and linked) for a parent configuration
     */
    public function getAllChildren($parentId);
}
