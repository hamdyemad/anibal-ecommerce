<?php

namespace Modules\SystemSetting\app\Repositories;

use Modules\SystemSetting\app\Interfaces\PointsSettingRepositoryInterface;
use Modules\SystemSetting\app\Models\PointsSetting;
use Illuminate\Support\Facades\DB;
use Modules\SystemSetting\app\Models\PointsSystem;

class PointsSettingRepository implements PointsSettingRepositoryInterface
{
    /**
     * Get all points settings with currencies
     */
    public function getAllSettings(array $filters = [], $perPage = 10)
    {
        $query = PointsSetting::with(['currency']);

        return ($perPage == 0) ? $query->get() : $query->paginate($perPage);
    }

    /**
     * Get points setting by ID
     */
    public function getSettingById($id)
    {
        return PointsSetting::with(['currency'])
            ->where('id', $id)
            ->first();
    }

    /**
     * Get points setting by currency ID
     */
    public function getSettingByCurrencyId($currencyId)
    {
        return PointsSetting::with(['currency'])
            ->where('currency_id', $currencyId)
            ->first();
    }

    /**
     * Create new points setting
     */
    public function createSetting(array $data)
    {
        return DB::transaction(function () use ($data) {
            return PointsSetting::create([
                'currency_id' => $data['currency_id'],
                'is_active' => $data['is_active'] ?? false,
                'points_value' => $data['points_value'] ?? 0,
                'welcome_points' => $data['welcome_points'] ?? 0,
            ]);
        });
    }

    /**
     * Update points setting
     */
    public function updateSetting($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $setting = $this->getSettingById($id);

            $setting->update([
                'currency_id' => $data['currency_id'] ?? $setting->currency_id,
                'is_active' => $data['is_active'] ?? $setting->is_active,
                'points_value' => $data['points_value'] ?? $setting->points_value,
                'welcome_points' => $data['welcome_points'] ?? $setting->welcome_points,
            ]);

            return $setting->fresh();
        });
    }

    /**
     * Toggle points setting active status
     */
    public function toggleStatus($id)
    {
        $setting = $this->getSettingById($id);
        $setting->update(['is_active' => !$setting->is_active]);
        return $setting->fresh();
    }


    public function getPointSystem() {
        return PointsSystem::latest()->first();
    }

    public function updatePointSystem($data) {
        return DB::transaction(function () use ($data) {
            $pointSystem = $this->getPointSystem();

            if (!$pointSystem) {
                $pointSystem = PointsSystem::create($data);
            } else {
                $pointSystem->update($data);
            }

            return $pointSystem->fresh();
        });
    }

    public function togglePointsSystemEnabled() {
        return DB::transaction(function () {
            $pointSystem = $this->getPointSystem();

            if (!$pointSystem) {
                $pointSystem = PointsSystem::create(['is_enabled' => true]);
            } else {
                $pointSystem->update(['is_enabled' => !$pointSystem->is_enabled]);
            }

            return $pointSystem->fresh();
        });
    }


}
