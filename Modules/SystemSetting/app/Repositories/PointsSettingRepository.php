<?php

namespace Modules\SystemSetting\app\Repositories;

use Modules\SystemSetting\app\Models\PointsSetting;
use Illuminate\Database\Eloquent\Collection;

class PointsSettingRepository
{
    /**
     * Get points setting by currency ID
     */
    public function getByCurrencyId(int $currencyId): ?PointsSetting
    {
        return PointsSetting::where('currency_id', $currencyId)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get all active points settings
     */
    public function getAllActive(): Collection
    {
        return PointsSetting::where('is_active', true)->get();
    }

    /**
     * Get points setting by ID
     */
    public function findById(int $id): ?PointsSetting
    {
        return PointsSetting::find($id);
    }

    /**
     * Create points setting
     */
    public function create(array $data): PointsSetting
    {
        return PointsSetting::create($data);
    }

    /**
     * Update points setting
     */
    public function update(int $id, array $data): bool
    {
        $pointsSetting = $this->findById($id);
        if (!$pointsSetting) {
            return false;
        }
        return $pointsSetting->update($data);
    }

    /**
     * Delete points setting
     */
    public function delete(int $id): bool
    {
        $pointsSetting = $this->findById($id);
        if (!$pointsSetting) {
            return false;
        }
        return $pointsSetting->delete();
    }
}
