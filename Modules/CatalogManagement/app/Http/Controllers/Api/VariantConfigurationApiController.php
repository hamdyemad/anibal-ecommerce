<?php

namespace Modules\CatalogManagement\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Modules\CatalogManagement\app\Http\Requests\Api\VariantByKeyRequest;
use Modules\CatalogManagement\app\Http\Resources\Api\VariantApiResource;
use Modules\CatalogManagement\app\Http\Resources\Api\VariantKeyApiResource;
use Modules\CatalogManagement\app\Http\Resources\Api\VariantWithChildrenApiResource;
use Modules\CatalogManagement\app\Services\Api\VariantConfigurationApiService;

class VariantConfigurationApiController extends Controller
{
    use Res;

    public function __construct(
        private VariantConfigurationApiService $service
    ) {}

    /**
     * Get all variant configuration keys
     * GET /api/variants/keys
     */
    public function keys()
    {
        $keys = $this->service->getAllKeys();
        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            VariantKeyApiResource::collection($keys)
        );
    }

    /**
     * Get variant tree by key ID
     * GET /api/variants/keys/{keyId}/tree
     */
    public function keyTree(string $keyId)
    {
        $key = $this->service->findKeyById((int) $keyId);

        if (!$key) {
            return $this->sendRes(
                config('responses.not_found')[app()->getLocale()],
                false,
                null,
                404
            );
        }

        $variants = $this->service->getKeyRootVariants((int) $keyId);

        $data = [
            'id' => $key->id,
            'name' => $key->getTranslation('name', app()->getLocale()),
            'type' => 'key',
            'created_at' => $key->created_at,
            'updated_at' => $key->updated_at,
            'children' => VariantWithChildrenApiResource::collection($variants)
        ];

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            $data
        );
    }


    /**
     * Get single variant configuration
     * GET /api/variants/{id}
     */
    public function show(string $id)
    {
        $variant = $this->service->getVariant((int) $id);

        if (!$variant) {
            return $this->sendRes(
                config('responses.not_found')[app()->getLocale()],
                false,
                null,
                404
            );
        }

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            new VariantApiResource($variant)
        );
    }

    /**
     * Get variant tree by variant ID (from key down to this variant)
     * GET /api/variants/{id}/tree
     */
    public function tree(string $id)
    {
        $variant = $this->service->getVariant((int) $id);

        if (!$variant) {
            return $this->sendRes(
                config('responses.not_found')[app()->getLocale()],
                false,
                null,
                404
            );
        }

        $selectedPath = $this->service->getSelectedPath((int) $id);
        $rootVariants = $this->service->getVariantsAtLevel($variant->key_id, null);

        $children = $rootVariants->map(function ($rootVariant) use ($selectedPath) {
            return $this->buildTreeWithPath($rootVariant, $selectedPath);
        })->toArray();

        $data = [
            'id' => $variant->key?->id,
            'name' => $variant->key?->getTranslation('name', app()->getLocale()),
            'type' => 'key',
            'selected_variant_id' => (int) $id,
            'selected_path' => $selectedPath,
            'children' => $children
        ];

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            $data
        );
    }

    /**
     * Get variants by key ID (flat list or with parent filter)
     * GET /api/variants/by-key/{keyId}
     */
    public function byKey(VariantByKeyRequest $request, string $keyId)
    {
        $parentId = $request->validated('parent_id');
        $variants = $this->service->getVariantsByKey((int) $keyId, $parentId ? (int) $parentId : null);

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            VariantApiResource::collection($variants)
        );
    }

    /**
     * Get children of a variant
     * GET /api/variants/{id}/children
     */
    public function children(string $id)
    {
        $children = $this->service->getVariantChildren((int) $id);

        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            VariantApiResource::collection($children)
        );
    }


    /**
     * Build tree with selected path highlighted
     */
    private function buildTreeWithPath($variant, array $selectedPath): array
    {
        $isSelected = in_array($variant->id, $selectedPath);
        $hasChildren = $variant->children->count() > 0;

        $children = [];
        if ($isSelected && $hasChildren) {
            $childVariants = $this->service->getVariantsAtLevel($variant->key_id, $variant->id);
            $children = $childVariants->map(function ($child) use ($selectedPath) {
                return $this->buildTreeWithPath($child, $selectedPath);
            })->toArray();
        }

        return [
            'id' => $variant->id,
            'name' => $variant->getTranslation('name', app()->getLocale()),
            'value' => $variant->value,
            'type' => $variant->type,
            'color' => $variant->type === 'color' ? $variant->value : null,
            'key_id' => $variant->key_id,
            'parent_id' => $variant->parent_id,
            'is_selected' => $isSelected,
            'has_children' => $hasChildren,
            'children_count' => $variant->children->count(),
            'created_at' => $variant->created_at,
            'updated_at' => $variant->updated_at,
            'children' => $children,
        ];
    }
}
