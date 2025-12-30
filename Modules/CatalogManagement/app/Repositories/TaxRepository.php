<?php

namespace Modules\CatalogManagement\app\Repositories;

use Modules\CatalogManagement\app\Interfaces\TaxRepositoryInterface;
use Modules\CatalogManagement\app\Models\Tax;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TaxRepository implements TaxRepositoryInterface
{
    /**
     * Get all taxes with optional pagination
     */
    public function getAllTaxes(int $perPage = 10, array $filters = [])
    {
        $query = $this->getTaxesQuery($filters);
        return $perPage > 0 ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Get taxes query for DataTables
     */
    public function getTaxesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        $query = Tax::with('translations');

        // Apply search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('percentage', 'like', "%{$search}%")
                    ->orWhereHas('translations', function ($tq) use ($search) {
                        $tq->where('lang_value', 'like', "%{$search}%");
                    });
            });
        }

        // Apply active filter
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        // Apply sorting
        if ($orderBy) {
            $query->orderBy($orderBy, $orderDirection);
        } else {
            $query->latest();
        }

        return $query;
    }

    /**
     * Get all taxes query (alias for getTaxesQuery)
     */
    public function getAllTaxesQuery(array $filters = [])
    {
        return $this->getTaxesQuery($filters);
    }

    /**
     * Get tax by ID
     */
    public function getTaxById(int $id)
    {
        return Tax::with('translations')->findOrFail($id);
    }

    /**
     * Create a new tax
     */
    public function createTax(array $data)
    {
        return DB::transaction(function () use ($data) {
            $tax = Tax::create([
                'percentage' => $data['percentage'],
                'is_active' => $data['is_active'] ?? true,
            ]);

            // Store translations
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $tax->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'name',
                            'lang_value' => $translation['name'],
                        ]);
                    }
                }
            }

            return $tax;
        });
    }

    /**
     * Update tax
     */
    public function updateTax(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $tax = Tax::findOrFail($id);

            $tax->update([
                'percentage' => $data['percentage'] ?? $tax->percentage,
                'is_active' => $data['is_active'] ?? $tax->is_active,
            ]);

            // Update translations
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $tax->translations()->updateOrCreate(
                            [
                                'lang_id' => $langId,
                                'lang_key' => 'name',
                            ],
                            [
                                'lang_value' => $translation['name'],
                            ]
                        );
                    }
                }
            }

            return $tax->fresh();
        });
    }

    /**
     * Delete tax
     */
    public function deleteTax(int $id)
    {
        $tax = Tax::findOrFail($id);
        $tax->translations()->delete();
        return $tax->delete();
    }

    /**
     * Get active taxes
     */
    public function getActiveTaxes()
    {
        return Tax::with('translations')->where('is_active', true)->get();
    }
}
