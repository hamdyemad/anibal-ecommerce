<?php

namespace Modules\Order\app\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RequestQuotationCollection extends ResourceCollection
{
    public $collects = RequestQuotationResource::class;

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
