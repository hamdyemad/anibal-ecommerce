<?php

namespace Modules\CatalogManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LanguageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\CatalogManagement\app\Services\VariantsConfigurationService;
use Modules\CatalogManagement\app\Actions\VariantsConfigurationAction;
use Modules\CatalogManagement\app\Http\Requests\VariantsConfigurationRequest;
use Modules\CatalogManagement\app\Http\Resources\VariantKeyTreeResource;
use Modules\CatalogManagement\app\Http\Resources\VariantsConfigurationResource;
use Modules\CatalogManagement\app\Http\Resources\VariantsConfigurationKeyResource;
use Modules\CatalogManagement\app\Models\VariantConfigurationKey;
use Modules\CatalogManagement\app\Models\VariantsConfiguration;
use Modules\CatalogManagement\app\Services\VariantConfigurationKeyService;

class VariantsConfigurationController extends Controller
{

    public function __construct(
        protected VariantsConfigurationService $variantsConfigService,
        protected VariantConfigurationKeyService $variantConfigKeyService,
        protected VariantsConfigurationAction $variantsConfigAction,
        protected LanguageService $languageService
    ) {
        $this->middleware('can:variants-configurations.index')->only(['index', 'datatable', 'tree', 'getParentsByKey']);
        $this->middleware('can:variants-configurations.create')->only(['create', 'store']);
        $this->middleware('can:variants-configurations.edit')->only(['edit', 'update']);
        $this->middleware('can:variants-configurations.delete')->only(['destroy']);
        $this->middleware('can:variants-configurations.show')->only(['show']);
        // Note: getVariantKeys, getVariantsByKey, and getKeyTree are API endpoints used by product forms
        // They should be accessible to anyone who can create/edit products
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $languages = $this->languageService->getAll();
        return view('catalogmanagement::variants-config.index', compact('languages'));
    }

    /**
     * Display tree view of variants configurations grouped by keys
     */
    public function tree()
    {
        $languages = $this->languageService->getAll();
        // Get all parent keys (keys without parent_key_id) with their children keys and variant configurations
        $filters = [
            'parent_key_id' => 'without'
        ];
        $variantKeys = $this->variantConfigKeyService->getAllVariantConfigurationKeys($filters, 0);
        // Transform through resource
        $treeData = VariantKeyTreeResource::collection($variantKeys);
        return view('catalogmanagement::variants-config.tree', compact('languages', 'treeData'));
    }

    /**
     * Get datatable data
     */
    public function datatable(Request $request)
    {
        // Handle search parameter - could be string (custom input) or array (DataTables)
        $search = $request->get('search');
        if (is_array($search)) {
            $searchValue = $search['value'] ?? null;
        } else {
            $searchValue = $search;
        }

        $data = [
            'page' => $request->get('page', 1),
            'draw' => $request->get('draw', 1),
            'start' => $request->get('start', 0),
            'length' => $request->get('length', 10),
            'per_page' => $request->get('per_page', 10),
            'orderColumnIndex' => $request->get('order')[0]['column'] ?? 0,
            'orderDirection' => $request->get('order')[0]['dir'] ?? 'desc',
            'search' => $searchValue,
            'created_date_from' => $request->get('created_date_from'),
            'created_date_to' => $request->get('created_date_to'),
        ];

        try {
            $response = $this->variantsConfigAction->getDataTable($data);

            \Illuminate\Support\Facades\Log::info('VariantsConfiguration Datatable Response', [
                'data_count' => count($response['data']),
                'totalRecords' => $response['totalRecords'],
                'filteredRecords' => $response['filteredRecords']
            ]);

            return response()->json([
                'draw' => $data['draw'],
                'data' => $response['data'],
                'recordsTotal' => $response['totalRecords'],
                'recordsFiltered' => $response['filteredRecords'],
                'current_page' => $response['dataPaginated']->currentPage(),
                'last_page' => $response['dataPaginated']->lastPage(),
                'per_page' => $response['dataPaginated']->perPage(),
                'total' => $response['dataPaginated']->total(),
                'from' => $response['dataPaginated']->firstItem(),
                'to' => $response['dataPaginated']->lastItem()
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('VariantsConfiguration Datatable Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'draw' => $data['draw'],
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($lang, $countryCode)
    {
        $languages = $this->languageService->getAll();
        $variantKeys = VariantConfigurationKey::withoutGlobalScopes()->with('translations')->get();
        $variantKeys = VariantsConfigurationKeyResource::collection($variantKeys)->resolve();
        $allVariantsConfigs = $this->variantsConfigService->getAll();
        return view('catalogmanagement::variants-config.form', compact('languages', 'variantKeys', 'allVariantsConfigs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($lang, $countryCode, VariantsConfigurationRequest $request)
    {
        $validated = $request->validated();
        \Log::info('VariantsConfiguration Store Request', [
            'validated' => $validated,
            'all_input' => $request->all()
        ]);
        try {
            $variantConfig = $this->variantsConfigService->create($validated);
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('variantsconfig.created_successfully'),
                    'redirect' => route('admin.variants-configurations.index')
                ]);
            }

            return redirect()->route('admin.variants-configurations.index')
                ->with('success', __('variantsconfig.created_successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('variantsconfig.error_creating') . ': ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('variantsconfig.error_creating') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($lang, $countryCode, Request $request, string $id)
    {
        $variantsConfig = $this->variantsConfigService->findById($id);
        // For API requests, load children recursively
        if ($request->wantsJson() || $request->is('api/*')) {
            if (!$variantsConfig) {
                return response()->json([
                    'error' => trans('catalogmanagement::variantsconfig.not_found')
                ], 404);
            }
            return response()->json(VariantsConfigurationResource::make($variantsConfig));
        }

        if (!$variantsConfig) {
            return redirect()->route('admin.variants-configurations.index')
                ->with('error', trans('catalogmanagement::variantsconfig.not_found'));
        }

        $languages = $this->languageService->getAll();
        return view('catalogmanagement::variants-config.show', compact('variantsConfig', 'languages'));
    }

    /**
     * Get variant configuration tree by key ID (for product form)
     */
    public function getKeyTree(string $keyId)
    {
        try {
            // Get variant key using service
            $key = $this->variantConfigKeyService->getVariantKeyTree($keyId);

            if (!$key) {
                return response()->json([
                    'error' => 'Variant key not found'
                ], 404);
            }

            // Get variants configuration for this key using service
            $variantsConfig = $this->variantsConfigService->getVariantsByKey($keyId);

            $result = [
                'id' => $key->id,
                'name' => $key->getTranslation('name', app()->getLocale()),
                'children' => $variantsConfig->map(function($variant) {
                    return [
                        'id' => $variant->id,
                        'name' => $variant->getTranslation('name', app()->getLocale()),
                        'children' => $this->buildVariantChildrenTree($variant->id)
                    ];
                })->toArray()
            ];

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error loading variant tree',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get variant configuration tree by variant ID
     * Returns the full tree from key with all variants at each level (for product form)
     */
    public function getVariantTree(string $id)
    {
        try {
            $variant = $this->variantsConfigService->findById($id);

            if (!$variant) {
                return response()->json([
                    'error' => 'Variant configuration not found'
                ], 404);
            }

            // Get the path from root to this variant (to know which ones are selected)
            $selectedPath = $this->getSelectedPath($variant);

            // Build the full tree starting from the key
            $result = [
                'id' => $variant->key ? $variant->key->id : null,
                'name' => $variant->key ? $variant->key->getTranslation('name', app()->getLocale()) : null,
                'type' => 'key',
                'selected_variant_id' => $id,
                'selected_path' => $selectedPath,
                'children' => $this->buildFullVariantTree($variant->key_id, null, $selectedPath),
            ];

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error loading variant tree',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the path of selected variant IDs from root to target
     */
    private function getSelectedPath($variant)
    {
        $path = [];
        $current = $variant;
        
        while ($current) {
            array_unshift($path, $current->id);
            $current = $current->parent_data;
        }
        
        return $path;
    }

    /**
     * Build full variant tree with all siblings at each level
     */
    private function buildFullVariantTree($keyId, $parentId = null, $selectedPath = [])
    {
        // Get all variants at this level
        $variants = VariantsConfiguration::with(['translations', 'children'])
            ->where('key_id', $keyId)
            ->when($parentId === null, function($q) {
                $q->whereNull('parent_id');
            }, function($q) use ($parentId) {
                $q->where('parent_id', $parentId);
            })
            ->get();

        return $variants->map(function($variant) use ($selectedPath) {
            $isSelected = in_array($variant->id, $selectedPath);
            $hasChildren = $variant->children->count() > 0;
            
            return [
                'id' => $variant->id,
                'name' => $variant->getTranslation('name', app()->getLocale()),
                'value' => $variant->value,
                'type' => $variant->type,
                'color' => $variant->type === 'color' ? $variant->value : null,
                'is_selected' => $isSelected,
                'has_children' => $hasChildren,
                'children_count' => $variant->children->count(),
                // Only load children for selected path to keep response size manageable
                'children' => $isSelected && $hasChildren 
                    ? $this->buildFullVariantTree($variant->key_id, $variant->id, $selectedPath)
                    : [],
            ];
        })->toArray();
    }

    /**
     * Get children for a variant configuration recursively (using service)
     */
    private function buildVariantChildrenTree($parentId)
    {
        $children = $this->variantsConfigService->getVariantChildren($parentId);

        return $children->map(function($child) {
            return [
                'id' => $child->id,
                'name' => $child->getTranslation('name', app()->getLocale()),
                'children' => $this->buildVariantChildrenTree($child->id)
            ];
        })->toArray();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($lang, $countryCode, string $id)
    {
        $variantsConfig = $this->variantsConfigService->findById($id);
        $languages = $this->languageService->getAll();
        $variantKeys = VariantConfigurationKey::withoutGlobalScopes()->with('translations')->get();
        $variantKeys = VariantsConfigurationKeyResource::collection($variantKeys)->resolve();
        $allVariantsConfigs = $this->variantsConfigService->getAll();

        if (!$variantsConfig) {
            return redirect()->route('admin.variants-configurations.index')
                ->with('error', trans('catalogmanagement::variantsconfig.not_found'));
        }

        return view('catalogmanagement::variants-config.form', compact('variantsConfig', 'languages', 'variantKeys', 'allVariantsConfigs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($lang, $countryCode, VariantsConfigurationRequest $request, string $id)
    {
        try {
            $validated = $request->validated();
            $variantKey = $this->variantsConfigService->update($id, $validated);
            if ($variantKey) {
                return response()->json([
                    'success' => true,
                    'message' => __('variantsconfig.updated_successfully'),
                    'redirect' => route('admin.variants-configurations.index')
                ]);
            }

            return redirect()->route('admin.variants-configurations.index')
                ->with('success', __('variantsconfig.updated_successfully'));
        } catch (\Exception $e) {
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('variantsconfig.error_updating') . ': ' . $e->getMessage()
                ], 422);
            }
            return redirect()->back()
                ->withInput()
                ->with('error', __('variantsconfig.error_updating') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($lang, $countryCode, Request $request, string $id)
    {
        try {
            $result = $this->variantsConfigService->delete($id);
            return response()->json([
                'success' => true,
                'message' => __('variantsconfig.deleted_successfully'),
                'redirect' => route('admin.variants-configurations.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('variantsconfig.error_deleting') . ': ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get parent variants by key for AJAX requests
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getParentsByKey(Request $request)
    {
        try {
            $keyId = $request->get('key_id');
            $parentId = $request->get('parent_id'); // Can be null for root level
            $currentId = $request->get('current_id');

            if (!$keyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Key ID is required'
                ], 400);
            }

            // Get variants with the same key and specific parent (or no parent if null)
            $parents = $this->variantsConfigService->getVariantsByKeyAndParent($keyId, $parentId, $currentId);

            $data = [];
            if ($parents && $parents->count() > 0) {
                $data = $parents->map(function ($parent) {
                    $parentName = $parent->getTranslation('name', app()->getLocale()) ?? 'No Name';

                    // Add parent hierarchy information
                    $displayName = $parentName;
                    if ($parent->parent_data) {
                        $grandParentName = $parent->parent_data->getTranslation('name', app()->getLocale()) ?? $parent->parent_data->value ?? 'No Name';
                        // Use appropriate arrow for RTL/LTR
                        $arrow = app()->getLocale() == 'ar' ? ' ← ' : ' → ';
                        $displayName = $parentName . $arrow . $grandParentName;
                    }

                    return [
                        'id' => $parent->id,
                        'name' => $parentName,
                        'display_name' => $displayName,
                        'has_parent' => $parent->parent_data ? true : false,
                    ];
                });
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching parent variants: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get variant configuration keys for API
     */
    public function getVariantKeys(Request $request)
    {
        try {
            \Log::info('Getting variant keys API call');

            $data = $this->variantsConfigService->getVariantKeysForApi();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getVariantKeys: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error fetching variant keys: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get variant configurations by key ID for API
     */
    public function getVariantsByKey(Request $request)
    {
        try {
            $keyId = $request->get('key_id');
            $parentId = $request->get('parent_id');

            if (!$keyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Key ID is required'
                ], 400);
            }

            $data = $this->variantsConfigService->getVariantsByKeyForApi($keyId, $parentId);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching variants: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Link a child configuration to a parent
     */
    public function linkChild(Request $request)
    {
        $request->validate([
            'parent_id' => 'required|exists:variants_configurations,id',
            'child_id' => 'required|exists:variants_configurations,id',
        ]);

        try {
            $result = $this->variantsConfigService->linkConfiguration(
                $request->parent_id,
                $request->child_id
            );

            return response()->json([
                'success' => true,
                'message' => $result 
                    ? __('catalogmanagement::variantsconfig.link_created_successfully')
                    : __('catalogmanagement::variantsconfig.link_already_exists')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('catalogmanagement::variantsconfig.error_creating_link') . ': ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Unlink a child configuration from a parent
     */
    public function unlinkChild(Request $request)
    {
        $request->validate([
            'parent_id' => 'required|exists:variants_configurations,id',
            'child_id' => 'required|exists:variants_configurations,id',
        ]);

        try {
            $this->variantsConfigService->unlinkConfiguration(
                $request->parent_id,
                $request->child_id
            );

            return response()->json([
                'success' => true,
                'message' => __('catalogmanagement::variantsconfig.link_removed_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('catalogmanagement::variantsconfig.error_removing_link') . ': ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Sync linked children for a parent configuration
     */
    public function syncLinkedChildren(Request $request)
    {
        $request->validate([
            'parent_id' => 'required|exists:variants_configurations,id',
            'child_ids' => 'required|array',
            'child_ids.*' => 'exists:variants_configurations,id',
        ]);

        try {
            $this->variantsConfigService->syncLinkedChildren(
                $request->parent_id,
                $request->child_ids
            );

            return response()->json([
                'success' => true,
                'message' => __('catalogmanagement::variantsconfig.links_synced_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('catalogmanagement::variantsconfig.error_syncing_links') . ': ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get linked children for a parent configuration
     */
    public function getLinkedChildren(Request $request, $lang, $countryCode, $id)
    {
        try {
            $linkedChildren = $this->variantsConfigService->getLinkedChildren($id);

            $data = $linkedChildren->map(function ($child) {
                return [
                    'id' => $child->id,
                    'name' => $child->getTranslation('name', app()->getLocale()),
                    'value' => $child->value,
                    'type' => $child->type,
                    'key_name' => $child->key ? $child->key->getTranslation('name', app()->getLocale()) : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('catalogmanagement::variantsconfig.error_fetching_linked_children') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all children (both direct and linked) for a parent configuration
     */
    public function getAllChildren(Request $request, $lang, $countryCode, $id)
    {
        try {
            $allChildren = $this->variantsConfigService->getAllChildren($id);

            $data = $allChildren->map(function ($child) {
                return [
                    'id' => $child->id,
                    'name' => $child->getTranslation('name', app()->getLocale()),
                    'value' => $child->value,
                    'type' => $child->type,
                    'key_name' => $child->key ? $child->key->getTranslation('name', app()->getLocale()) : null,
                    'is_linked' => $child->pivot ? true : false, // If pivot exists, it's a linked child
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('catalogmanagement::variantsconfig.error_fetching_children') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all variants for linking (simple list)
     */
    public function getAllForLinking(Request $request)
    {
        try {
            $variants = $this->variantsConfigService->getAll();

            $data = $variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'name' => $variant->getTranslation('name', app()->getLocale()),
                    'value' => $variant->value,
                    'type' => $variant->type,
                    'key_name' => $variant->key ? $variant->key->getTranslation('name', app()->getLocale()) : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching variants: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the link ID between parent and child variant configurations
     * Used when storing product variants to track the specific parent-child relationship
     */
    public function getLinkId(Request $request)
    {
        $request->validate([
            'parent_id' => 'required|exists:variants_configurations,id',
            'child_id' => 'required|exists:variants_configurations,id',
        ]);

        try {
            $link = DB::table('variants_configurations_links')
                ->where('parent_config_id', $request->parent_id)
                ->where('child_config_id', $request->child_id)
                ->first();

            if (!$link) {
                return response()->json([
                    'success' => false,
                    'message' => __('catalogmanagement::variantsconfig.link_not_found'),
                    'link_id' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'link_id' => $link->id,
                'parent_id' => $link->parent_config_id,
                'child_id' => $link->child_config_id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching link ID: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get or create link ID with complete hierarchy path
     */
    public function getLinkIdWithPath(Request $request)
    {
        $request->validate([
            'path' => 'required|array|min:2',
            'path.*' => 'required|exists:variants_configurations,id',
        ]);

        try {
            $path = $request->path;
            $parentId = $path[count($path) - 2]; // Second to last
            $childId = $path[count($path) - 1];  // Last

            // Try to find existing link with the same path
            $link = DB::table('variants_configurations_links')
                ->where('parent_config_id', $parentId)
                ->where('child_config_id', $childId)
                ->whereRaw('JSON_EXTRACT(path, "$") = ?', [json_encode($path)])
                ->first();

            if (!$link) {
                // Create new link with path
                $linkId = DB::table('variants_configurations_links')->insertGetId([
                    'parent_config_id' => $parentId,
                    'child_config_id' => $childId,
                    'path' => json_encode($path),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Log::info('Created new variant link with path', [
                    'link_id' => $linkId,
                    'parent_id' => $parentId,
                    'child_id' => $childId,
                    'path' => $path
                ]);

                return response()->json([
                    'success' => true,
                    'link_id' => $linkId,
                    'parent_id' => $parentId,
                    'child_id' => $childId,
                    'path' => $path,
                    'created' => true
                ]);
            }

            return response()->json([
                'success' => true,
                'link_id' => $link->id,
                'parent_id' => $link->parent_config_id,
                'child_id' => $link->child_config_id,
                'path' => json_decode($link->path, true),
                'created' => false
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getLinkIdWithPath', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching/creating link ID with path: ' . $e->getMessage()
            ], 500);
        }
    }
}