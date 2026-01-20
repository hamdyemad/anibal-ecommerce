<?php

namespace Modules\Refund\app\Services;

use Modules\Refund\app\Interfaces\RefundRequestRepositoryInterface;
use Modules\Refund\app\Services\RefundNotificationService;

class RefundRequestService
{
    protected $repository;
    protected $notificationService;

    public function __construct(
        RefundRequestRepositoryInterface $repository,
        RefundNotificationService $notificationService
    ) {
        $this->repository = $repository;
        $this->notificationService = $notificationService;
    }

    public function getAllRefunds(array $filters, int $perPage = 15)
    {
        return $this->repository->getAllPaginated($filters, $perPage);
    }

    public function getRefundById(int $id)
    {
        return $this->repository->findById($id);
    }

    public function createRefund(array $data, $user)
    {
        // Create refund through repository (observer will send notifications)
        return $this->repository->createRefundWithVendorSplit($data, $user);
    }

    public function updateRefundStatus(int $id, array $data, $user)
    {
        return $this->repository->updateRefundStatus($id, $data, $user);
    }

    public function getStatistics(array $filters)
    {
        return $this->repository->getStatistics($filters);
    }

    public function canUserAccessRefund(int $id, $user): bool
    {
        return $this->repository->canUserAccessRefund($id, $user);
    }

    public function approveRefund(int $id)
    {
        return $this->repository->approveRefund($id);
    }

    public function cancelRefund(int $id, string $cancellationReason)
    {
        return $this->repository->cancelRefund($id, $cancellationReason);
    }

    public function updateNotes(int $id, string $notes, bool $isAdmin = false)
    {
        return $this->repository->updateNotes($id, $notes, $isAdmin);
    }

    public function getRefundWithRelations(int $id, array $relations = [])
    {
        return $this->repository->getRefundWithRelations($id, $relations);
    }

    /**
     * Get refund statistics for dashboard cards
     */
    public function getRefundStatistics(?int $vendorId = null): array
    {
        $query = \Modules\Refund\app\Models\RefundRequest::query();
        
        // Filter by vendor if provided
        if ($vendorId) {
            $query->where('vendor_id', $vendorId);
        }
        
        // Get all statuses from model
        $statuses = array_keys(\Modules\Refund\app\Models\RefundRequest::STATUSES);
        
        // Build status data array with count and amount for each status
        $statusData = [];
        foreach ($statuses as $status) {
            $statusQuery = (clone $query)->where('status', $status);
            $statusData[$status] = [
                'count' => $statusQuery->count(),
                'amount' => $statusQuery->sum('total_refund_amount'),
                'amount_formatted' => number_format($statusQuery->sum('total_refund_amount'), 2),
            ];
        }
        
        // Total refund requests
        $totalRefunds = (clone $query)->count();
        
        // Total refunded amount (only completed refunds)
        $totalRefundedAmount = (clone $query)->where('status', 'refunded')->sum('total_refund_amount');
        
        return [
            'total_refunds' => $totalRefunds,
            'total_refunded_amount' => number_format($totalRefundedAmount, 2),
            'status_data' => $statusData,
        ];
    }
}
