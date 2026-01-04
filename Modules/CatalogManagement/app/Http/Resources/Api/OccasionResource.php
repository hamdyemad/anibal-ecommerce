<?php

namespace Modules\CatalogManagement\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OccasionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Filter occasion products - only approved and active
        $filteredProducts = $this->occasionProducts->filter(function ($occasionProduct) {
            $vendorProduct = $occasionProduct->vendorProductVariant?->vendorProduct;
            if (!$vendorProduct) {
                return false;
            }
            return $vendorProduct->status === 'approved' && $vendorProduct->is_active;
        });

        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name', app()->getLocale()) ?? '',
            'title' => $this->getTranslation('title', app()->getLocale()) ?? '',
            'subtitle' => $this->getTranslation('sub_title', app()->getLocale()) ?? '',
            'slug' => $this->slug,
            'image' => $this->attachments()
                ->where('type', 'image')
                ->first()?->path ? asset('storage/' . $this->attachments()->where('type', 'image')->first()->path) : null,
            'status' => (bool) $this->is_active,
            'start_date' => $this->start_date?->format('d M, Y'),
            'end_date' => $this->end_date?->format('d M, Y'),
            'occasionProductsCount' => $filteredProducts->count(),
            'occasionProducts' => OccasionProductResource::collection($filteredProducts),
        ];
    }
}
