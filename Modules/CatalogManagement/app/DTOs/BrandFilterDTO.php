<?php

namespace Modules\CatalogManagement\app\DTOs;

use App\DTOs\FilterDTO;
use Modules\CatalogManagement\app\Models\Brand;
use Modules\CategoryManagment\app\Models\Category;
use Modules\CategoryManagment\app\Models\Department;
use Modules\CategoryManagment\app\Models\SubCategory;
use Modules\Vendor\app\Models\Vendor;

class BrandFilterDTO extends FilterDTO
{
    private array $errors = [];

    public function __construct(
        public ?string $search = null,
        public ?string $brand_id = null,
        public ?string $department_id = null,
        public ?string $category_id = null,
        public ?string $sub_category_id = null,
        public ?string $vendor_id = null,
        public ?string $sort_by = null,
        public ?string $sort_type = null,
        public ?string $char = null,
        public ?int $per_page = null,
        public ?string $paginated = null,
    ) {}

    /**
     * Create DTO from HTTP request
     */
    public static function fromRequest($request): self
    {
        return new self(
            search: $request->input('search'),
            brand_id: $request->input('brand_id'),
            department_id: $request->input('department_id'),
            category_id: $request->input('category_id'),
            sub_category_id: $request->input('sub_category_id'),
            vendor_id: $request->input('vendor_id'),
            sort_by: $request->input('sort_by'),
            sort_type: $request->input('sort_type'),
            char: $request->input('char'),
            per_page: $request->integer('per_page', 12),
            paginated: $request->input('paginated', null),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'brand_id' => $this->brand_id,
            'department_id' => $this->department_id,
            'category_id' => $this->category_id,
            'sub_category_id' => $this->sub_category_id,
            'vendor_id' => $this->vendor_id,
            'sort_by' => $this->sort_by,
            'sort_type' => $this->sort_type,
            'char' => $this->char,
            'per_page' => $this->per_page,
            'paginated' => $this->paginated,
        ], fn($value) => $value !== null);
    }

    public function validate(): bool
    {
        $this->errors = [];

        if ($this->brand_id && !$this->brandExists($this->brand_id)) {
            $this->errors['brand_id'][] = __('validation.brand_id_not_exist');
        }

        if ($this->sort_by && !in_array($this->sort_by, ['created_at', 'name'])) {
            $this->errors['sort_by'][] = __('validation.sort_by_invalid');
        }

        if ($this->sort_type && !in_array($this->sort_type, ['asc', 'desc'])) {
            $this->errors['sort_type'][] = __('validation.sort_type_invalid');
        }

        if ($this->department_id && !$this->departmentExists($this->department_id)) {
            $this->errors['department_id'][] = __('validation.department_id_not_exist');
        }

        if ($this->category_id && !$this->categoryExists($this->category_id)) {
            $this->errors['category_id'][] = __('validation.category_id_not_exist');
        }

        if ($this->sub_category_id && !$this->subCategoryExists($this->sub_category_id)) {
            $this->errors['sub_category_id'][] = __('validation.sub_category_id_not_exist');
        }

        if ($this->vendor_id && !$this->vendorExists($this->vendor_id)) {
            $this->errors['vendor_id'][] = __('validation.vendor_id_not_exist');
        }

        if ($this->char && !in_array($this->char, ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'])) {
            $this->errors['char'][] = __('validation.char_invalid');
        }

        return count($this->errors) === 0;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function brandExists(string $brandId): bool
    {
        return Brand::where('id', $brandId)->orWhere('slug', $brandId)->exists();
    }

    private function departmentExists(string $departmentId): bool
    {
        return Department::where('id', $departmentId)->orWhere('slug', $departmentId)->exists();
    }

    private function vendorExists(string $vendorId): bool
    {
        return Vendor::where('id', $vendorId)->orWhere('slug', $vendorId)->exists();
    }

    private function categoryExists(string $categoryId): bool
    {
        return Category::where('id', $categoryId)->orWhere('slug', $categoryId)->exists();
    }

    private function subCategoryExists(string $subCategoryId): bool
    {
        return SubCategory::where('id', $subCategoryId)->orWhere('slug', $subCategoryId)->exists();
    }
}
