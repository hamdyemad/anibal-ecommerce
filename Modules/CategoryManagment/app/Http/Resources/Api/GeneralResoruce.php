<?php

namespace Modules\CategoryManagment\app\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneralResoruce extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'title' => $this->name,
            'slug' => $this->slug,
            'image' => formatImage($this->image),
            'cover' => formatImage($this->cover),
            'type' => $this->type ?? 'General',
        ];
        if($this->type == 'brand') {
            $data['icon'] = formatImage($this->image);

        } else {
            $data['icon'] = formatImage($this->icon);
        }
        return $data;
    }
}
