<?php

namespace Modules\CatalogManagement\app\Repositories\Api;

use Modules\CatalogManagement\app\Models\BundleCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\CatalogManagement\app\Interfaces\Api\BundleCategoryApiRepositoryInterface;

class BundleCategoryApiRepository implements BundleCategoryApiRepositoryInterface
{

    public function getAll(array $filters = [], $per_page = 10)
    {
        $query = BundleCategory::with(['translations'])
        ->withCount('bundles')
        ->active()
        ->filter($filters)
        ->orderBy('created_at', 'desc');
        return ($per_page == 0) ? $query->get() : $query->paginate($per_page);
    }


    /**
     * Get bundle category by ID
     */
    public function getBundleCategoryById($id)
    {
        return BundleCategory::with(['translations', 'attachments', 'bundles' => function($q) {
            $q->approved();
        }])
        ->withCount('bundles')
        ->where('slug', $id)
        ->orWhere('id', $id)->firstOrFail();
    }

}
