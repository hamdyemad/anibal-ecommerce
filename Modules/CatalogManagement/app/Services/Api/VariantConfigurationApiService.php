<?php

namespace Modules\CatalogManagement\app\Services\Api;

use Modules\CatalogManagement\app\Interfaces\Api\VariantConfigurationApiRepositoryInterface;

class VariantConfigurationApiService
{
    public function __construct(
        private VariantConfigurationApiRepositoryInterface $repository
    ) {}

    /**
     * Get all variant configuration keys
     */
    public function getAllKeys()
    {
        return $this->repository->getAllKeys();
    }

    /**
     * Find key by ID
     */
    public function findKeyById(int $keyId)
    {
        return $this->repository->findKeyById($keyId);
    }

    /**
     * Get root variants for a key (for key tree)
     */
    public function getKeyRootVariants(int $keyId)
    {
        return $this->repository->getVariantsByKeyId($keyId, null);
    }

    /**
     * Get single variant by ID
     */
    public function getVariant(int $id)
    {
        return $this->repository->findById($id);
    }

    /**
     * Get selected path from root to variant
     */
    public function getSelectedPath(int $id): array
    {
        return $this->repository->getSelectedPath($id);
    }

    /**
     * Get variants at a specific level
     */
    public function getVariantsAtLevel(int $keyId, ?int $parentId = null)
    {
        return $this->repository->getVariantsAtLevel($keyId, $parentId);
    }

    /**
     * Get variants by key ID
     */
    public function getVariantsByKey(int $keyId, ?int $parentId = null)
    {
        return $this->repository->getVariantsByKeyId($keyId, $parentId);
    }

    /**
     * Get children of a variant
     */
    public function getVariantChildren(int $id)
    {
        return $this->repository->getChildrenById($id);
    }
}
