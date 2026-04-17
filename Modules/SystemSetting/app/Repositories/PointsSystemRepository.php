<?php

namespace Modules\SystemSetting\app\Repositories;

use Modules\SystemSetting\app\Models\PointsSystem;

class PointsSystemRepository
{
    /**
     * Get the latest points system configuration
     */
    public function getLatest(): ?PointsSystem
    {
        return PointsSystem::latest()->first();
    }

    /**
     * Check if points system is enabled
     */
    public function isEnabled(): bool
    {
        $pointsSystem = $this->getLatest();
        return $pointsSystem && $pointsSystem->is_enabled;
    }

    /**
     * Get points system by ID
     */
    public function findById(int $id): ?PointsSystem
    {
        return PointsSystem::find($id);
    }

    /**
     * Create or update points system
     */
    public function createOrUpdate(array $data): PointsSystem
    {
        return PointsSystem::updateOrCreate(
            ['id' => $data['id'] ?? null],
            $data
        );
    }
}
