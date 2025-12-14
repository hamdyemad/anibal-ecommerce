<?php

namespace Modules\SystemSetting\app\Services;

use Modules\SystemSetting\app\Repositories\FeatureRepository;

class FeatureService
{
    protected $featureRepository;

    public function __construct(FeatureRepository $featureRepository)
    {
        $this->featureRepository = $featureRepository;
    }

    public function getAllFeatures()
    {
        return $this->featureRepository->all();
    }

    public function getFeatureById($id)
    {
        return $this->featureRepository->find($id);
    }

    public function createFeature(array $data)
    {
        return $this->featureRepository->create($data);
    }

    public function updateFeature($id, array $data)
    {
        return $this->featureRepository->update($id, $data);
    }

    public function deleteFeature($id)
    {
        return $this->featureRepository->delete($id);
    }

    public function filterFeatures($filters)
    {
        return $this->featureRepository->filter($filters);
    }
}
