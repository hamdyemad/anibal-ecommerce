<?php

namespace Modules\Order\app\Services\Api;

use Modules\Order\app\Interfaces\Api\RequestQuotationApiRepositoryInterface;
use Modules\Order\app\Models\RequestQuotation;
use Modules\Customer\app\Models\Customer;

class RequestQuotationApiService
{
    public function __construct(
        protected RequestQuotationApiRepositoryInterface $repository
    ) {}

    public function getCustomerQuotations(Customer $customer, int $perPage = 15, array $filters = [])
    {
        return $this->repository->getCustomerQuotations($customer->id, $perPage, $filters);
    }

    public function getQuotationForCustomer(int $id, Customer $customer)
    {
        return $this->repository->findForCustomer($id, $customer->id);
    }

    public function createQuotation(array $data, $file = null, ?int $customerId = null)
    {
        if ($file) {
            $data['file'] = $file->store('request-quotations', 'public');
        }

        if ($customerId) {
            $data['customer_id'] = $customerId;
        }

        // Map address_id to customer_address_id
        if (isset($data['address_id'])) {
            $data['customer_address_id'] = $data['address_id'];
            unset($data['address_id']);
        }

        // Set default status
        $data['status'] = RequestQuotation::STATUS_PENDING;

        return $this->repository->create($data);
    }

    public function respondToOffer(RequestQuotation $quotation, Customer $customer, string $action): array
    {
        if (!$quotation->canRespondToOffer()) {
            return [
                'success' => false,
                'message_key' => 'cannot_respond_to_offer',
            ];
        }

        if ($action === 'accept') {
            // Notification is handled by NotificationObserver via HasNotifications trait
            $quotation = $this->repository->acceptOffer($quotation->id, $customer->id);

            return [
                'success' => true,
                'message_key' => 'offer_accepted_successfully',
                'quotation' => $quotation,
            ];
        } else {
            // Notification is handled by NotificationObserver via HasNotifications trait
            $quotation = $this->repository->rejectOffer($quotation->id, $customer->id);

            return [
                'success' => true,
                'message_key' => 'offer_rejected_successfully',
                'quotation' => $quotation,
            ];
        }
    }
}
