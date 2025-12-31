<?php

namespace Modules\Order\app\Interfaces\Api;

interface RequestQuotationApiRepositoryInterface
{
    public function getCustomerQuotations(int $customerId, int $perPage = 15, array $filters = []);
    
    public function findForCustomer(int $id, int $customerId);
    
    public function create(array $data);
    
    public function acceptOffer(int $id, int $customerId);
    
    public function rejectOffer(int $id, int $customerId);
}
