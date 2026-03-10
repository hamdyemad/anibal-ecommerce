<?php

namespace Modules\CatalogManagement\app\Services;

use Modules\CatalogManagement\app\Repositories\VariantsConfigurationRepository;

class VariantsConfigurationService
{
    protected $variantsConfigRepository;

    public function __construct(VariantsConfigurationRepository $variantsConfigRepository)
    {
        $this->variantsConfigRepository = $variantsConfigRepository;
    }

    public function getAll()
    {
        return $this->variantsConfigRepository->getAll();
    }

    /**
     * Get all variants configurations with pagination
     */
    public function getAllPaginated(array $filters = [], int $perPage = 20)
    {
        return $this->variantsConfigRepository->getAllPaginated($filters, $perPage);
    }

    public function findById($id)
    {
        return $this->variantsConfigRepository->findById($id);
    }

    public function create(array $data)
    {
        return $this->variantsConfigRepository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->variantsConfigRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->variantsConfigRepository->delete($id);
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
        return $this->variantsConfigRepository->getParentsByKey($keyId, $currentId);
    }

    /**
     * Get variants by key ID and parent ID
     *
     * @param int $keyId
     * @param int|null $parentId
     * @param int|null $currentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVariantsByKeyAndParent($keyId, $parentId = null, $currentId = null)
    {
        return $this->variantsConfigRepository->getVariantsByKeyAndParent($keyId, $parentId, $currentId);
    }

    /**
     * Get variant configuration keys for API
     *
     * @return array
     */
    public function getVariantKeysForApi()
    {
        return $this->variantsConfigRepository->getVariantKeysForApi();
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
        return $this->variantsConfigRepository->getVariantsByKeyForApi($keyId, $parentId);
    }

    /**
     * Get variants configuration by key ID
     *
     * @param int $keyId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVariantsByKey($keyId)
    {
        return $this->variantsConfigRepository->getVariantsByKey($keyId);
    }

    /**
     * Get variant children recursively
     *
     * @param int $parentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVariantChildren($parentId)
    {
        return $this->variantsConfigRepository->getVariantChildren($parentId);
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
        return $this->variantsConfigRepository->linkConfiguration($parentId, $childId);
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
        return $this->variantsConfigRepository->unlinkConfiguration($parentId, $childId);
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
        return $this->variantsConfigRepository->syncLinkedChildren($parentId, $childIds);
    }

    /**
     * Get linked children for a parent configuration
     *
     * @param int $parentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLinkedChildren($parentId)
    {
        return $this->variantsConfigRepository->getLinkedChildren($parentId);
    }

    /**
     * Get all children (both direct and linked) for a parent configuration
     *
     * @param int $parentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllChildren($parentId)
    {
        return $this->variantsConfigRepository->getAllChildren($parentId);
    }
}
