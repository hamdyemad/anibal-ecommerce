<?php

namespace Modules\CatalogManagement\app\Services;

use Modules\CatalogManagement\app\Interfaces\BundleCategoryRepositoryInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BundleCategoryService
{
    protected $bundleCategoryRepository;

    public function __construct(
        BundleCategoryRepositoryInterface $bundleCategoryRepository
    ) {
        $this->bundleCategoryRepository = $bundleCategoryRepository;
    }

    /**
     * Create bundle category
     */
    public function createBundleCategory(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Generate slug from first translation name
            $firstTranslation = collect($data['translations'])->first();
            $name = $firstTranslation['name'] ?? 'bundle-category';
            $data['slug'] = Str::slug($name) . '-' . Str::random(6);

            $bundleCategory = $this->bundleCategoryRepository->createBundleCategory($data);

            // Handle image upload
            if (isset($data['image']) && $data['image']) {
                $path = $data['image']->store("bundle-categories/{$bundleCategory->id}", 'public');
                $bundleCategory->attachments()->create([
                    'path' => $path,
                    'type' => 'image'
                ]);
            }

            return $bundleCategory;
        });
    }

    /**
     * Update bundle category
     */
    public function updateBundleCategory($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $bundleCategory = $this->bundleCategoryRepository->updateBundleCategory($id, $data);

            // Handle image upload
            if (isset($data['image']) && $data['image']) {
                // Delete old image
                $oldImage = $bundleCategory->attachments()->where('type', 'image')->first();
                if ($oldImage) {
                    if (Storage::disk('public')->exists($oldImage->path)) {
                        Storage::disk('public')->delete($oldImage->path);
                    }
                    $oldImage->delete();
                }

                // Store new image
                $path = $data['image']->store("bundle-categories/{$bundleCategory->id}", 'public');
                $bundleCategory->attachments()->create([
                    'path' => $path,
                    'type' => 'image'
                ]);
            }

            return $bundleCategory;
        });
    }

    /**
     * Delete bundle category
     */
    public function deleteBundleCategory($id)
    {
        return DB::transaction(function () use ($id) {
            $bundleCategory = $this->bundleCategoryRepository->getBundleCategoryById($id);

            // Delete attachments
            foreach ($bundleCategory->attachments as $attachment) {
                if (Storage::disk('public')->exists($attachment->path)) {
                    Storage::disk('public')->delete($attachment->path);
                }
                $attachment->delete();
            }

            return $this->bundleCategoryRepository->deleteBundleCategory($id);
        });
    }

    /**
     * Toggle bundle category status
     */
    public function toggleBundleCategoryStatus($id)
    {
        return $this->bundleCategoryRepository->toggleBundleCategoryStatus($id);
    }
}
