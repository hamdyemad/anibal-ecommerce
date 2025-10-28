<?php

namespace App\Interfaces;

interface ActivityRepositoryInterface
{
    /**
     * Get all activities with filters and pagination
     */
    public function getAllActivities(array $filters = [], int $perPage = 15);

    /**
     * Get activity by ID
     */
    public function getActivityById(int $id);

    /**
     * Create a new activity
     */
    public function createActivity(array $data);

    /**
     * Update activity
     */
    public function updateActivity(int $id, array $data);

    /**
     * Delete activity
     */
    public function deleteActivity(int $id);

    /**
     * Get active activities
     */
    public function getActiveActivities();
}
