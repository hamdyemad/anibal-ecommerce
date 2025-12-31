<?php

namespace Modules\Order\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Modules\Order\app\Services\Api\RequestQuotationApiService;
use Modules\Order\app\Http\Requests\Api\StoreRequestQuotationRequest;
use Modules\Order\app\Http\Requests\Api\RespondQuotationOfferRequest;
use Modules\Order\app\Http\Resources\RequestQuotationResource;

class RequestQuotationApiController extends Controller
{
    use Res;

    public function __construct(
        protected RequestQuotationApiService $service
    ) {}

    /**
     * Store a new quotation request
     */
    public function store(StoreRequestQuotationRequest $request)
    {
        $customerId = auth('sanctum')->check() ? auth('sanctum')->id() : null;

        $quotation = $this->service->createQuotation(
            $request->validated(),
            $request->file('file'),
            $customerId
        );

        return $this->sendRes(
            config('responses.quotation_created_successfully')[app()->getLocale()],
            true,
            new RequestQuotationResource($quotation),
            [],
            201
        );
    }

    /**
     * Get customer's quotations
     */
    public function index()
    {
        /** @var \Modules\Customer\app\Models\Customer $customer */
        $customer = auth('sanctum')->user();
        
        if (!$customer) {
            return $this->sendRes(
                config('responses.unauthorized')[app()->getLocale()],
                false,
                [],
                [],
                401
            );
        }
        
        $perPage = request()->input('per_page', 15);
        
        $filters = [
            'status' => request()->input('status'),
            'search' => request()->input('search'),
        ];
        
        $quotations = $this->service->getCustomerQuotations($customer, (int) $perPage, $filters);

        return $this->sendRes(
            config('responses.quotations_retrieved_successfully')[app()->getLocale()],
            true,
            RequestQuotationResource::collection($quotations)
        );
    }

    /**
     * Get single quotation details
     */
    public function show($id)
    {
        /** @var \Modules\Customer\app\Models\Customer $customer */
        $customer = auth('sanctum')->user();
        
        if (!$customer) {
            return $this->sendRes(
                config('responses.unauthorized')[app()->getLocale()],
                false,
                [],
                [],
                401
            );
        }
        
        $quotation = $this->service->getQuotationForCustomer((int) $id, $customer);

        if (!$quotation) {
            return $this->sendRes(
                config('responses.quotation_not_found')[app()->getLocale()],
                false,
                [],
                [],
                404
            );
        }

        return $this->sendRes(
            config('responses.quotation_retrieved_successfully')[app()->getLocale()],
            true,
            new RequestQuotationResource($quotation)
        );
    }

    /**
     * Respond to offer (accept or reject)
     */
    public function respondToOffer(RespondQuotationOfferRequest $request, $id)
    {
        /** @var \Modules\Customer\app\Models\Customer $customer */
        $customer = auth('sanctum')->user();
        
        if (!$customer) {
            return $this->sendRes(
                config('responses.unauthorized')[app()->getLocale()],
                false,
                [],
                [],
                401
            );
        }
        
        $quotation = $this->service->getQuotationForCustomer((int) $id, $customer);

        if (!$quotation) {
            return $this->sendRes(
                config('responses.quotation_not_found')[app()->getLocale()],
                false,
                [],
                [],
                404
            );
        }

        $result = $this->service->respondToOffer($quotation, $customer, $request->action);

        if (!$result['success']) {
            return $this->sendRes(
                config('responses.' . $result['message_key'])[app()->getLocale()],
                false,
                [],
                [],
                400
            );
        }

        return $this->sendRes(
            config('responses.' . $result['message_key'])[app()->getLocale()],
            true,
            new RequestQuotationResource($result['quotation'])
        );
    }
}
