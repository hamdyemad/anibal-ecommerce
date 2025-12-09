<?php

namespace Modules\CatalogManagement\app\Interfaces\Api;

interface BundleCategoryApiRepositoryInterface
{

    // Get All Bundle Categories per page
    public function getAll(array $filters = [], $per_page);


    /**
     * Get bundle category by ID
     */
    public function getBundleCategoryById($id);

}
