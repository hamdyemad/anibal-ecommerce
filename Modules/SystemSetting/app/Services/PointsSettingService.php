<?php

namespace Modules\SystemSetting\app\Services;

use Modules\SystemSetting\app\Interfaces\PointsSettingRepositoryInterface;

class PointsSettingService
{
    protected $pointsSettingRepository;

    public function __construct(
        PointsSettingRepositoryInterface $pointsSettingRepository
    ) {
        $this->pointsSettingRepository = $pointsSettingRepository;
    }

    /**
     * Get all points settings
     */
    public function getAllSettings(array $filters = [], $perPage = 10)
    {
        return $this->pointsSettingRepository->getAllSettings($filters, $perPage);
    }

    /**
     * Get points setting by ID
     */
    public function getSettingById($id)
    {
        return $this->pointsSettingRepository->getSettingById($id);
    }

    /**
     * Get points setting by currency ID
     */
    public function getSettingByCurrencyId($currencyId)
    {
        return $this->pointsSettingRepository->getSettingByCurrencyId($currencyId);
    }

    /**
     * Create new points setting
     */
    public function createSetting(array $data)
    {
        return $this->pointsSettingRepository->createSetting($data);
    }

    /**
     * Update points setting
     */
    public function updateSetting($id, array $data)
    {
        return $this->pointsSettingRepository->updateSetting($id, $data);
    }

    /**
     * Toggle points setting active status
     */
    public function toggleStatus($id)
    {
        return $this->pointsSettingRepository->toggleStatus($id);
    }

    public function getPointSystem()
    {
        return $this->pointsSettingRepository->getPointSystem();
    }

    /**
     * Update points system
     */
    public function updatePointSystem($data)
    {
        return $this->pointsSettingRepository->updatePointSystem($data);
    }

    /**
     * Toggle points system enabled status
     */
    public function togglePointsSystemEnabled()
    {
        return $this->pointsSettingRepository->togglePointsSystemEnabled();
    }

}
