<?php

namespace Modules\CatalogManagement\app\Actions;

use Modules\CatalogManagement\app\Interfaces\PromocodeRepositoryInterface;
use Modules\CatalogManagement\app\Models\Promocode;

class PromocodeAction
{
    public function __construct(
        protected PromocodeRepositoryInterface $promocodeRepositoryInterface
    ) {}

    public function getDatatableData(array $data)
    {
        $perPage = $data['per_page'] ?? $data['length'] ?? 10;
        $page = $data['page'] ?? 1;

        $filters = [
            'active' => $data['active'] ?? null,
            'type' => $data['type'] ?? '',
            'dedicated_to' => $data['dedicated_to'] ?? '',
            'valid_from' => $data['valid_from'] ?? '',
            'valid_until' => $data['valid_until'] ?? '',
            'search' => $data['search'] ?? '',
        ];

        $query = $this->promocodeRepositoryInterface->getPromocodesQuery($filters);

        // Search logic (if not already in repository)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            // If search is an array from DataTables
            if (is_array($search)) {
                $search = $search['value'] ?? '';
            }

            if (!empty($search)) {
                $query->where('code', 'like', "%{$search}%");
            }
        }

        $totalRecords = Promocode::count();
        $filteredRecords = $query->count();

        $promocodes = $query->paginate($perPage, ['*'], 'page', $page);

        $formattedData = [];
        $index = ($page - 1) * $perPage + 1;
        foreach ($promocodes as $promocode) {
            $formattedData[] = [
                'index' => $index++,
                'id' => $promocode->id,
                'code' => $promocode->code,
                'maximum_of_use' => $promocode->maximum_of_use,
                'type' => $promocode->type,
                'value' => $promocode->value,
                'valid_from' => $promocode->valid_from->format('Y-m-d'),
                'valid_until' => $promocode->valid_until->format('Y-m-d'),
                'dedicated_to' => $promocode->dedicated_to,
                'is_active' => $promocode->is_active,
                'created_at' => $promocode->created_at,
            ];
        }

        return [
            'data' => $formattedData,
            'totalRecords' => $totalRecords,
            'filteredRecords' => $filteredRecords,
            'dataPaginated' => $promocodes
        ];
    }
}
