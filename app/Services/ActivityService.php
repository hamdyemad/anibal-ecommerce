<?php

namespace App\Services;

use App\Interfaces\ActivityRepositoryInterface;
use Illuminate\Support\Facades\Log;

class ActivityService
{
    protected $activityRepository;

    public function __construct(ActivityRepositoryInterface $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    /**
     * Get all activities with filters and pagination
     */
    public function getAllActivities(array $filters = [], int $perPage = 15)
    {
        try {
            return $this->activityRepository->getAllActivities($filters, $perPage);
        } catch (\Exception $e) {
            Log::error('Error fetching activities: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get activities query for DataTables
     */
    public function getActivitiesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        try {
            return $this->activityRepository->getActivitiesQuery($filters, $orderBy, $orderDirection);
        } catch (\Exception $e) {
            Log::error('Error fetching activities query: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get activity by ID
     */
    public function getActivityById(int $id)
    {
        try {
            return $this->activityRepository->getActivityById($id);
        } catch (\Exception $e) {
            Log::error('Error fetching activity: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new activity with translations
     */
    public function createActivity(array $data)
    {
        try {
            // Validate and prepare data
            $preparedData = $this->prepareActivityData($data);
            
            return $this->activityRepository->createActivity($preparedData);
        } catch (\Exception $e) {
            Log::error('Error creating activity: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update activity with translations
     */
    public function updateActivity(int $id, array $data)
    {
        try {
            // Validate and prepare data
            $preparedData = $this->prepareActivityData($data);
            
            return $this->activityRepository->updateActivity($id, $preparedData);
        } catch (\Exception $e) {
            Log::error('Error updating activity: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete activity
     */
    public function deleteActivity(int $id)
    {
        try {
            return $this->activityRepository->deleteActivity($id);
        } catch (\Exception $e) {
            Log::error('Error deleting activity: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get active activities
     */
    public function getActiveActivities()
    {
        try {
            return $this->activityRepository->getActiveActivities();
        } catch (\Exception $e) {
            Log::error('Error fetching active activities: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Prepare activity data for storage
     */
    private function prepareActivityData(array $data): array
    {
        return [
            'translations' => $data['translations'] ?? [],
            'active' => $data['active'] ?? 0,
        ];
    }
}
