<?php

namespace Modules\SystemSetting\app\Services;

use Modules\SystemSetting\app\Repositories\SliderRepository;

class SliderService
{
    protected $sliderRepository;

    public function __construct(SliderRepository $sliderRepository)
    {
        $this->sliderRepository = $sliderRepository;
    }

    public function getAllSliders()
    {
        return $this->sliderRepository->all();
    }

    public function getSliderById($id)
    {
        return $this->sliderRepository->find($id);
    }

    public function createSlider(array $data)
    {
        return $this->sliderRepository->create($data);
    }

    public function updateSlider($id, array $data)
    {
        return $this->sliderRepository->update($id, $data);
    }

    public function deleteSlider($id)
    {
        return $this->sliderRepository->delete($id);
    }

    public function filterSliders($filters)
    {
        return $this->sliderRepository->filter($filters);
    }
}
