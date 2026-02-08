<?php

namespace Modules\Order\app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Customer\app\Transformers\CustomerApiResource;
use Modules\Customer\app\Transformers\AddressResource;

class RequestQuotationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'quotation_number' => $this->quotation_number,
            'notes' => $this->notes,
            'file' => $this->file ? asset('storage/' . $this->file) : null,
            'customer' => $this->whenLoaded('customer', function() {
                return $this->customer ? new CustomerApiResource($this->customer) : null;
            }),
            'address' => $this->whenLoaded('customerAddress', function() {
                return $this->customerAddress ? new AddressResource($this->customerAddress) : null;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // Multi-vendor data
            'vendors' => $this->whenLoaded('vendors', function() {
                return RequestQuotationVendorResource::collection($this->vendors);
            }),
        ];
    }
}
