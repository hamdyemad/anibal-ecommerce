<?php

namespace Modules\AreaSettings\app\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Get minimum shipping cost for this city
        $minShippingCost = $this->getMinimumShippingCost();
        
        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name', app()->getLocale()),
            'slug' => $this->slug,
            'default' => $this->default,
            'image' => ($this->image) ? asset('storage/' . $this->image->path) : '',
            'country' => new CountryResource($this->country),
            'shipping' => [
                'min_cost' => $minShippingCost,
                'has_shipping' => $minShippingCost !== null,
            ],
            'active' => $this->active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Get minimum shipping cost for this city
     */
    private function getMinimumShippingCost()
    {
        // Check if shippings relationship is loaded
        if ($this->relationLoaded('shippings')) {
            $activeShippings = $this->shippings->where('active', 1);
            return $activeShippings->isNotEmpty() ? (float) $activeShippings->min('cost') : null;
        }
        
        // Otherwise query directly
        return (float) \Modules\Order\app\Models\Shipping::whereHas('cities', function($query) {
            $query->where('cities.id', $this->id);
        })
        ->where('active', 1)
        ->min('cost');
    }
}
