<?php

namespace Modules\SystemSetting\app\Interfaces;

interface PointsSettingRepositoryInterface
{
    /**
     * Get all points settings with currencies
     */
    public function getAllSettings(array $filters = [], $perPage = 10);

    /**
     * Get points setting by ID
     */
    public function getSettingById($id);

    /**
     * Get points setting by currency ID
     */
    public function getSettingByCurrencyId($currencyId);

    /**
     * Create new points setting
     */
    public function createSetting(array $data);

    /**
     * Update points setting
     */
    public function updateSetting($id, array $data);

    /**
     * Toggle points setting active status
     */
    public function toggleStatus($id);

    /**
     * Get Point System And Update
     */
    public function getPointSystem();
    public function updatePointSystem($data);
    public function togglePointsSystemEnabled();

}
