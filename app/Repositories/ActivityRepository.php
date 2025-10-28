<?php

namespace App\Repositories;

use App\Interfaces\ActivityRepositoryInterface;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;

class ActivityRepository implements ActivityRepositoryInterface
{
    /**
     * Get all activities with filters and pagination
     */
    public function getAllActivities(array $filters = [], int $perPage = 15)
    {
        $query = Activity::with('translations');

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_value', 'like', "%{$search}%");
                });
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

        return $query->paginate($perPage);
    }

    /**
     * Get activities query for DataTables
     */
    public function getActivitiesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        $query = Activity::with('translations');

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_value', 'like', "%{$search}%");
                });
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

        // Apply sorting
        if ($orderBy !== null) {
            if (is_array($orderBy)) {
                // Sorting by translated name or description
                $langId = $orderBy['lang_id'];
                $langKey = $orderBy['key'] ?? 'name';
                $query->leftJoin('translations as t_sort', function($join) use ($langId, $langKey) {
                    $join->on('activities.id', '=', 't_sort.translatable_id')
                         ->where('t_sort.translatable_type', '=', 'App\\Models\\Activity')
                         ->where('t_sort.lang_id', '=', $langId)
                         ->where('t_sort.lang_key', '=', $langKey);
                })
                ->orderBy('t_sort.lang_value', $orderDirection)
                ->select('activities.*');
            } else {
                // Sorting by regular column
                $query->orderBy($orderBy, $orderDirection);
            }
        }

        return $query;
    }

    /**
     * Get activity by ID
     */
    public function getActivityById(int $id)
    {
        return Activity::with('translations')->findOrFail($id);
    }

    /**
     * Create a new activity
     */
    public function createActivity(array $data)
    {
        return DB::transaction(function () use ($data) {
            $activity = Activity::create([
                'active' => $data['active'] ?? 0,
            ]);

            // Set translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $activity->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'name',
                            'lang_value' => $translation['name'],
                        ]);
                    }
                    if (isset($translation['description'])) {
                        $activity->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'description',
                            'lang_value' => $translation['description'],
                        ]);
                    }
                }
            }
            
            return $activity;
        });
    }

    /**
     * Update activity
     */
    public function updateActivity(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $activity = Activity::findOrFail($id);

            $activity->update([
                'active' => $data['active'] ?? 0,
            ]);

            // Update translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $activity->translations()->updateOrCreate(
                            [
                                'lang_id' => $langId,
                                'lang_key' => 'name',
                            ],
                            [
                                'lang_value' => $translation['name'],
                            ]
                        );
                    }
                    if (isset($translation['description'])) {
                        $activity->translations()->updateOrCreate(
                            [
                                'lang_id' => $langId,
                                'lang_key' => 'description',
                            ],
                            [
                                'lang_value' => $translation['description'],
                            ]
                        );
                    }
                }
            }

            $activity->refresh();
            $activity->load('translations');

            return $activity;
        });
    }

    /**
     * Delete activity
     */
    public function deleteActivity(int $id)
    {
        $activity = Activity::findOrFail($id);
        $activity->translations()->delete();
        return $activity->delete();
    }

    /**
     * Get active activities
     */
    public function getActiveActivities()
    {
        return Activity::with('translations')->where('active', 1)
            ->get();
    }
}
