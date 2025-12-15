<?php

namespace Modules\Order\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingCalculationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'shipping_cost' => $this['shipping_cost'],
            'breakdown' => $this['breakdown'],
            'address' => $this['address'],
        ];
    }
}
