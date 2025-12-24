<?php

namespace Modules\Order\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Modules\Order\app\Models\RequestQuotation;
use Modules\Order\app\Http\Requests\Api\StoreRequestQuotationRequest;

class RequestQuotationApiController extends Controller
{
    use Res;

    public function store(StoreRequestQuotationRequest $request)
    {
        $data = $request->validated();

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('request-quotations', 'public');
            $data['file'] = $path;
        }

        $quotation = RequestQuotation::create($data);

        return $this->sendRes(
            __('order::request-quotation.created_successfully'),
            true,
            [
                'id' => $quotation->id,
                'name' => $quotation->name,
                'email' => $quotation->email,
                'phone' => $quotation->phone,
                'address' => $quotation->address,
                'notes' => $quotation->notes,
                'file' => $quotation->file ? asset('storage/' . $quotation->file) : null,
                'status' => $quotation->status,
                'created_at' => $quotation->created_at,
            ],
            [],
            201
        );
    }
}
