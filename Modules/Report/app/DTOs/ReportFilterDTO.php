<?php

namespace Modules\Report\app\DTOs;

class ReportFilterDTO
{
    public function __construct(
        public ?string $from = null,
        public ?string $to = null,
        public ?string $search = null,
        public ?string $status = null,
        public ?string $gender = null,  // For Registered Users Report
        public ?string $type = null,    // For Orders Report (pending, completed, etc.)
        public ?string $category = null, // For Product Report
        public ?string $vendor = null,   // For Product Report
        public ?string $city_id = null,  // For Area Users Report (city/area filter)
        public int $page = 1,
        public int $per_page = 10,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            from: $data['from'] ?? $data['from_date'] ?? null,
            to: $data['to'] ?? $data['to_date'] ?? null,
            search: $data['search'] ?? null,
            status: $data['status'] ?? null,
            gender: $data['gender'] ?? null,
            type: $data['type'] ?? null,
            category: $data['category'] ?? null,
            vendor: $data['vendor'] ?? null,
            city_id: $data['city_id'] ?? null,
            page: $data['page'] ?? 1,
            per_page: $data['per_page'] ?? 10,
        );
    }
}
