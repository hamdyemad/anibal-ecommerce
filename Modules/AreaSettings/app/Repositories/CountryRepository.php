<?php

namespace Modules\AreaSettings\app\Repositories;

use Modules\AreaSettings\app\Interfaces\CountryRepositoryInterface;
use Modules\AreaSettings\app\Models\Country;
use Illuminate\Support\Facades\DB;

class CountryRepository implements CountryRepositoryInterface
{
    /**
     * Get all countries with filters and pagination
     */
    public function getAllCountries(array $filters = [], ?int $perPage = 15)
    {
        $query = Country::with('translations');

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_value', 'like', "%{$search}%");
                })->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Active filter
        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('active', $filters['active']);
        }

        // Date from filter
        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        // Date to filter
        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        // Return paginated or all records
        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Get countries query for DataTables
     */
    public function getCountriesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        $query = Country::with('translations');
        
        // Debug: Log filters in repository
        \Log::info('CountryRepository filters:', $filters);

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            \Log::info('Applying search filter:', ['search' => $search]);
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_value', 'like', "%{$search}%");
                })
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('phone_code', 'like', "%{$search}%")
                ;
            });
        }

        // Active filter
        if (isset($filters['active']) && $filters['active'] !== '') {
            \Log::info('Applying active filter:', ['active' => $filters['active']]);
            $query->where('active', $filters['active']);
        }

        // Date from filter
        if (!empty($filters['created_date_from'])) {
            \Log::info('Applying date from filter:', ['date_from' => $filters['created_date_from']]);
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }

        // Date to filter
        if (!empty($filters['created_date_to'])) {
            \Log::info('Applying date to filter:', ['date_to' => $filters['created_date_to']]);
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        // Apply sorting
        if ($orderBy !== null) {
            if (is_array($orderBy)) {
                // Sorting by translated name
                $langId = $orderBy['lang_id'];
                $query->leftJoin('translations as t_sort', function($join) use ($langId) {
                    $join->on('countries.id', '=', 't_sort.translatable_id')
                         ->where('t_sort.translatable_type', '=', 'Modules\\AreaSettings\\Models\\Country')
                         ->where('t_sort.lang_id', '=', $langId)
                         ->where('t_sort.lang_key', '=', 'name');
                })
                ->orderBy('t_sort.lang_value', $orderDirection)
                ->select('countries.*');
            } else {
                // Sorting by regular column
                $query->orderBy($orderBy, $orderDirection);
            }
        }

        return $query;
    }

    /**
     * Get country by ID
     */
    public function getCountryById(int $id)
    {
        return Country::with('translations')->findOrFail($id);
    }

    /**
     * Create a new country
     */
    public function createCountry(array $data)
    {
        return DB::transaction(function () use ($data) {
            $country = Country::create([
                'code' => $data['code'],
                'phone_code' => $data['phone_code'] ?? null,
                'active' => $data['active'] ?? 0,
            ]);

            // Set translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $country->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'name',
                            'lang_value' => $translation['name'],
                        ]);
                    }
                }
            }
            
            return $country;
        });
    }

    /**
     * Update country
     */
    public function updateCountry(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $country = Country::findOrFail($id);

            $country->update([
                'code' => $data['code'],
                'phone_code' => $data['phone_code'] ?? null,
                'active' => $data['active'] ?? 0,
            ]);

            // Update translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $country->translations()->updateOrCreate(
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

            $country->refresh();
            $country->load('translations');

            return $country;
        });
    }

    /**
     * Delete country
     */
    public function deleteCountry(int $id)
    {
        $country = Country::findOrFail($id);
        $country->translations()->delete();
        return $country->delete();
    }

    /**
     * Get active countries
     */
    public function getActiveCountries()
    {
        return Country::with('translations')->where('active', 1)
            ->get();
    }
}
