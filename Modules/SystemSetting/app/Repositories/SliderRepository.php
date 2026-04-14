<?php

namespace Modules\SystemSetting\app\Repositories;

use Modules\SystemSetting\app\Models\Slider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SliderRepository
{
    public function all()
    {
        return Slider::with('attachments')->orderBy('sort_order')->get();
    }

    public function find($id)
    {
        return Slider::with('attachments')->findOrFail($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $slider = Slider::create([
                'slider_link' => $data['slider_link'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
            ]);

            // Handle translations
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translationData) {
                    $lang = \App\Models\Language::find($langId);
                    if ($lang) {
                        $slider->setTranslation('title', $lang->code, $translationData['title'] ?? '');
                        $slider->setTranslation('description', $lang->code, $translationData['description'] ?? '');
                    }
                }
            }

            // Handle image upload
            if (isset($data['image']) && $data['image']) {
                $this->storeImage($slider, $data['image']);
            }

            return $slider;
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $slider = Slider::findOrFail($id);

            $slider->update([
                'slider_link' => $data['slider_link'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
            ]);

            // Handle translations
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translationData) {
                    $lang = \App\Models\Language::find($langId);
                    if ($lang) {
                        $slider->setTranslation('title', $lang->code, $translationData['title'] ?? '');
                        $slider->setTranslation('description', $lang->code, $translationData['description'] ?? '');
                    }
                }
            }

            // Handle image upload
            if (isset($data['image']) && $data['image']) {
                // Delete old image
                $slider->attachments()->where('type', 'image')->delete();
                $this->storeImage($slider, $data['image']);
            }

            return $slider;
        });
    }

    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            $slider = Slider::findOrFail($id);

            // Delete image
            $slider->attachments()->forceDelete();

            return $slider->delete();
        });
    }

    public function filter($filters)
    {
        return Slider::filter($filters)->with('attachments')->orderBy('sort_order')->get();
    }

    private function storeImage($slider, $image)
    {
        $path = $image->store('sliders', 'public');
        $slider->attachments()->create([
            'type' => 'image',
            'path' => $path,
        ]);
    }
}
