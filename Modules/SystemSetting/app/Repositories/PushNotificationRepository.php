<?php

namespace Modules\SystemSetting\app\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Customer\app\Models\Customer;
use Modules\Customer\app\Models\CustomerFcmToken;
use Modules\SystemSetting\app\Interfaces\PushNotificationRepositoryInterface;
use Modules\SystemSetting\app\Models\PushNotification;
use Modules\SystemSetting\app\Services\FirebaseService;

class PushNotificationRepository implements PushNotificationRepositoryInterface
{
    protected FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function all(array $filters = [])
    {
        $query = PushNotification::with(['createdBy', 'customers'])
            ->latest();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query;
    }

    public function find($id)
    {
        return PushNotification::with(['createdBy', 'customers'])->findOrFail($id);
    }

    public function createAndSend(array $data): PushNotification
    {
        DB::beginTransaction();

        try {
            // Create notification record
            $notification = PushNotification::create([
                'type' => $data['type'],
                'title' => [
                    'en' => $data['title_en'],
                    'ar' => $data['title_ar'],
                ],
                'description' => [
                    'en' => $data['description_en'],
                    'ar' => $data['description_ar'],
                ],
                'image' => $data['image'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Get target customers
            if ($data['type'] === PushNotification::TYPE_ALL) {
                $customerIds = Customer::active()->pluck('id')->toArray();
            } else {
                $customerIds = $data['customer_ids'] ?? [];
            }

            // Attach customers to notification
            if (!empty($customerIds)) {
                $notification->customers()->attach($customerIds);
            }

            DB::commit();

            // Send notifications
            $this->sendNotification($notification);

            return $notification->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create push notification: ' . $e->getMessage());
            throw $e;
        }
    }

    public function delete($id)
    {
        $notification = PushNotification::findOrFail($id);

        // Delete image if exists
        if ($notification->image && Storage::disk('public')->exists($notification->image)) {
            Storage::disk('public')->delete($notification->image);
        }

        return $notification->delete();
    }

    /**
     * Send the push notification to all attached customers
     */
    protected function sendNotification(PushNotification $notification): void
    {
        $customerIds = $notification->customers()->pluck('customers.id')->toArray();

        if (empty($customerIds)) {
            return;
        }

        // Get all FCM tokens for these customers
        $fcmTokens = CustomerFcmToken::whereIn('customer_id', $customerIds)
            ->pluck('fcm_token', 'customer_id')
            ->toArray();

        if (empty($fcmTokens)) {
            return;
        }

        // Get titles and descriptions using model method
        $titles = $notification->getTranslations('title');
        $descriptions = $notification->getTranslations('description');

        $imageUrl = $notification->image ? asset('storage/' . $notification->image) : null;

        // Send to each customer's tokens
        foreach ($fcmTokens as $customerId => $token) {
            // Get customer's preferred language (default to 'en')
            $customer = Customer::find($customerId);
            $lang = $customer->language ?? 'en';

            $title = $titles[$lang] ?? $titles['en'] ?? '';
            $body = $descriptions[$lang] ?? $descriptions['en'] ?? '';

            $this->firebaseService->sendToToken(
                $token,
                $title,
                $body,
                $imageUrl,
                ['notification_id' => $notification->id]
            );
        }
    }
}
