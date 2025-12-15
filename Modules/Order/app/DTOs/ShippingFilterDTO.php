<?php

namespace Modules\Order\app\DTOs;

class ShippingFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?bool $active = null,
        public ?string $created_date_from = null,
        public ?string $created_date_to = null,
        public ?int $city_id = null,
        public ?int $category_id = null,
        public ?int $country_id = null,
        public int $per_page = 15,
        public bool $paginated = true,
    ) {}

    /**
     * Create DTO from request
     */
    public static function fromRequest($request): self
    {
        return new self(
            search: $request->input('search'),
            active: $request->boolean('active', null),
            created_date_from: $request->input('created_date_from'),
            created_date_to: $request->input('created_date_to'),
            city_id: $request->input('city_id'),
            category_id: $request->input('category_id'),
            country_id: $request->input('country_id'),
            per_page: $request->input('per_page', 15),
            paginated: $request->boolean('paginated', true),
        );
    }

    /**
     * Validate the DTO
     */
    public function validate(): bool
    {
        return true;
    }

    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return [];
    }

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'active' => $this->active,
            'created_date_from' => $this->created_date_from,
            'created_date_to' => $this->created_date_to,
            'city_id' => $this->city_id,
            'category_id' => $this->category_id,
            'country_id' => $this->country_id,
            'per_page' => $this->per_page,
            'paginated' => $this->paginated,
        ];
    }
}
