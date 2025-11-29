<?php

namespace Modules\CatalogManagement\app\DTOs;

use App\DTOs\FilterDTO;
use Modules\CategoryManagment\app\Models\Department;
use Modules\Vendor\app\Models\Vendor;

class BrandFilterDTO extends FilterDTO
{
    private array $errors = [];

    public function __construct(
        public ?string $search = null,
        public ?string $department_id = null,
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
            department_id: $request->input('department_id'),
            vendor_id: $request->input('vendor_id'),
            sort_by: $request->input('sort_by'),
            sort_type: $request->input('sort_type'),
            char: $request->input('char'),
            per_page: $request->integer('per_page', 15),
            paginated: $request->input('paginated', null),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'department_id' => $this->department_id,
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

        if ($this->sort_by && !in_array($this->sort_by, ['created_at', 'name'])) {
            $this->errors['sort_by'][] = __('validation.sort_by_invalid');
        }

        if ($this->sort_type && !in_array($this->sort_type, ['asc', 'desc'])) {
            $this->errors['sort_type'][] = __('validation.sort_type_invalid');
        }

        if ($this->department_id && !$this->departmentExists($this->department_id)) {
            $this->errors['department_id'][] = __('validation.department_id_not_exist');
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

    private function departmentExists(string $departmentId): bool
    {
        return Department::where('id', $departmentId)->orWhere('slug', $departmentId)->exists();
    }

    private function vendorExists(string $vendorId): bool
    {
        return Vendor::where('id', $vendorId)->orWhere('slug', $vendorId)->exists();
    }
}
