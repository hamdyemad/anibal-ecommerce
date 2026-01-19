<?php

namespace Modules\Refund\app\Repositories;

use Modules\Refund\app\Interfaces\RefundRequestRepositoryInterface;
use Modules\Refund\app\Models\RefundRequest;

class RefundRequestRepository implements RefundRequestRepositoryInterface
{
    protected $model;

    public function __construct(RefundRequest $model)
    {
        $this->model = $model;
    }

    public function getAllPaginated(array $filters = [], int $perPage = 15)
    {
        $query = $this->model
            ->with(['order', 'customer', 'vendor', 'items.orderProduct']);
        
        
        return $query->filter($filters)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id)
    {
        return $this->model
            ->with(['order', 'customer', 'vendor', 'items.orderProduct'])
            ->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $refund = $this->findById($id);
        $refund->update($data);
        return $refund->fresh();
    }

    public function delete(int $id)
    {
        $refund = $this->findById($id);
        return $refund->delete();
    }

    public function getStatistics(array $filters = [])
    {
        $query = $this->model->filter($filters);

        return [
            'total' => $query->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
            'picked_up' => (clone $query)->where('status', 'picked_up')->count(),
            'refunded' => (clone $query)->where('status', 'refunded')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'total_amount' => (clone $query)->sum('total_refund_amount'),
        ];
    }

    public function canUserAccessRefund(int $refundId, $user): bool
    {
        $refund = $this->findById($refundId);

        // Admin can access all
        if ($user->isAdmin()) {
            return true;
        }

        // Vendor can access their refunds
        if ($user->vendor_id && $refund->vendor_id === $user->vendor_id) {
            return true;
        }

        // Customer can access their refunds
        if ($refund->customer_id === $user->id) {
            return true;
        }

        return false;
    }

    public function canUserCancelRefund(int $refundId, $user): bool
    {
        $refund = $this->findById($refundId);

        // Only customer can cancel
        if ($refund->customer_id !== $user->id) {
            return false;
        }

        // Can only cancel pending requests
        if ($refund->status !== 'pending') {
            return false;
        }

        return true;
    }

    public function createRefundWithVendorSplit(array $data, $user)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Get order
            $order = \Modules\Order\app\Models\Order::findOrFail($data['order_id']);

            // Verify customer owns this order
            if ($order->customer_id !== $user->id) {
                throw new \Exception('Unauthorized access to this order');
            }

            // Group items by vendor
            $itemsByVendor = $this->groupItemsByVendor($data['items']);
            $createdRefunds = [];

            // Create independent refund requests for each vendor
            foreach ($itemsByVendor as $vendorId => $vendorItems) {
                // Create basic refund request
                $refundRequest = $this->create([
                    'order_id' => $order->id,
                    'customer_id' => $order->customer_id,
                    'vendor_id' => $vendorId,
                    'country_id' => $order->country_id,
                    'status' => 'pending',
                    'reason' => $data['reason'],
                    'customer_notes' => $data['customer_notes'] ?? null,
                ]);

                // Add items
                foreach ($vendorItems as $item) {
                    $orderProduct = $item['order_product'];

                    \Modules\Refund\app\Models\RefundRequestItem::create([
                        'refund_request_id' => $refundRequest->id,
                        'order_product_id' => $orderProduct->id,
                        'vendor_id' => $vendorId,
                        'quantity' => $item['quantity'],
                        'unit_price' => $orderProduct->price,
                        'total_price' => $orderProduct->price * $item['quantity'],
                        'reason' => $item['reason'],
                    ]);
                }

                // Calculate totals
                $refundRequest->calculateTotals();
                $createdRefunds[] = $refundRequest->fresh(['items.orderProduct', 'order', 'vendor']);
            }

            \Illuminate\Support\Facades\DB::commit();

            return $createdRefunds;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            throw $e;
        }
    }

    public function updateRefundStatus(int $id, array $data, $user)
    {
        // Check authorization
        if (!$this->canUserAccessRefund($id, $user)) {
            throw new \Exception('Unauthorized');
        }

        $refund = $this->findById($id);
        $oldStatus = $refund->status;
        $newStatus = $data['status'];

        // Update status
        $refund = $this->update($id, [
            'status' => $newStatus,
            'vendor_notes' => $data['notes'] ?? null,
        ]);

        // Create history record
        $this->createHistoryRecord($id, $oldStatus, $newStatus, $user->id, $data['notes'] ?? null);

        return $refund;
    }

    public function cancelRefund(int $id, $user)
    {
        // Check if user can cancel
        if (!$this->canUserCancelRefund($id, $user)) {
            throw new \Exception('Cannot cancel refund request in current status');
        }

        $refund = $this->findById($id);
        $oldStatus = $refund->status;

        $refund = $this->update($id, ['status' => 'cancelled']);

        // Create history record
        $this->createHistoryRecord($id, $oldStatus, 'cancelled', $user->id);

        return $refund;
    }

    public function getRefundWithRelations(int $refundId, array $relations = [])
    {
        return $this->model->with($relations)->findOrFail($refundId);
    }

    public function approveRefund(int $id)
    {
        $refund = $this->findById($id);
        
        if ($refund->status != 'pending') {
            throw new \Exception('Cannot approve this refund request');
        }

        $oldStatus = $refund->status;
        
        $refund = $this->update($id, [
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        // Create history record
        $this->createHistoryRecord($id, $oldStatus, 'approved', auth()->id());

        return $refund;
    }

    public function rejectRefund(int $id, string $rejectionReason)
    {
        $refund = $this->findById($id);
        
        if ($refund->status != 'pending') {
            throw new \Exception('Cannot reject this refund request');
        }

        $oldStatus = $refund->status;
        
        $refund = $this->update($id, [
            'status' => 'rejected',
            'vendor_notes' => $rejectionReason,
        ]);

        // Create history record
        $this->createHistoryRecord($id, $oldStatus, 'rejected', auth()->id(), $rejectionReason);

        return $refund;
    }

    public function updateNotes(int $id, string $notes, bool $isAdmin = false)
    {
        $notesField = $isAdmin ? 'admin_notes' : 'vendor_notes';
        
        return $this->update($id, [
            $notesField => $notes,
        ]);
    }

    /**
     * Group refund items by vendor
     */
    protected function groupItemsByVendor(array $items): array
    {
        $grouped = [];
        
        foreach ($items as $item) {
            $orderProduct = \Modules\Order\app\Models\OrderProduct::findOrFail($item['order_product_id']);
            $vendorId = $orderProduct->vendor_id;
            
            if (!isset($grouped[$vendorId])) {
                $grouped[$vendorId] = [];
            }
            
            $grouped[$vendorId][] = [
                'order_product_id' => $item['order_product_id'],
                'quantity' => $item['quantity'],
                'reason' => $item['reason'] ?? null,
                'order_product' => $orderProduct,
            ];
        }
        
        return $grouped;
    }

    /**
     * Create history record for status change
     */
    protected function createHistoryRecord(int $refundId, ?string $oldStatus, string $newStatus, ?int $userId, ?string $notes = null): void
    {
        \Modules\Refund\app\Models\RefundRequestHistory::create([
            'refund_request_id' => $refundId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'user_id' => $userId,
            'notes' => $notes,
        ]);
    }

    // Method removed
}
