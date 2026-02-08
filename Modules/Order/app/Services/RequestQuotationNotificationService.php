<?php

namespace Modules\Order\app\Services;

use Illuminate\Support\Facades\Log;
use Modules\Order\app\Models\RequestQuotationVendor;
use Modules\SystemSetting\app\Services\FirebaseService;
use Modules\Customer\app\Models\CustomerFcmToken;

class RequestQuotationNotificationService
{
    public function __construct(
        protected FirebaseService $firebaseService
    ) {}

    /**
     * Send notification when vendor sends offer to customer
     */
    public function notifyCustomerOfferReceived(RequestQuotationVendor $quotationVendor): void
    {
        try {
            $customer = $quotationVendor->requestQuotation->customer;
            
            if (!$customer) {
                return;
            }

            // Send AdminNotification (for in-app notifications)
            \App\Models\AdminNotification::notify(
                type: 'quotation_offer_received',
                title: 'order::request-quotation.notification_customer_offer_title',
                description: 'order::request-quotation.notification_customer_offer_message',
                url: route('admin.request-quotations.view-offers', [
                    'lang' => app()->getLocale(),
                    'countryCode' => $quotationVendor->requestQuotation->country->code ?? 'eg',
                    'id' => $quotationVendor->request_quotation_id,
                ]),
                icon: 'uil-envelope-receive',
                color: 'info',
                notifiable: $quotationVendor,
                data: [
                    'vendor' => $quotationVendor->vendor->name,
                    'price' => number_format($quotationVendor->offer_price ?? 0, 2) . ' ' . currency(),
                    'quotation_number' => $quotationVendor->requestQuotation->quotation_number,
                    'quotation_vendor_id' => $quotationVendor->id,
                ],
                userId: $customer->id,
                vendorId: null
            );

            // Send Firebase notification
            $this->sendFirebaseToCustomer($customer->id, $quotationVendor);

            // Send notification to admin
            $this->notifyAdminOfferReceived($quotationVendor);

        } catch (\Exception $e) {
            Log::error('Failed to send offer received notification: ' . $e->getMessage());
        }
    }

    /**
     * Send Firebase notification to customer
     */
    protected function sendFirebaseToCustomer(int $customerId, RequestQuotationVendor $quotationVendor): void
    {
        try {
            $tokens = CustomerFcmToken::where('customer_id', $customerId)
                ->pluck('fcm_token')
                ->toArray();

            if (empty($tokens)) {
                return;
            }

            $title = __('order::request-quotation.notification_customer_offer_title');
            $body = __('order::request-quotation.notification_customer_offer_message', [
                'vendor' => $quotationVendor->vendor->name,
                'price' => number_format($quotationVendor->offer_price ?? 0, 2) . ' ' . currency(),
            ]);

            $data = [
                'type' => 'quotation_offer_received',
                'quotation_id' => $quotationVendor->request_quotation_id,
                'quotation_vendor_id' => $quotationVendor->id,
                'vendor_name' => $quotationVendor->vendor->name,
                'price' => (string) ($quotationVendor->offer_price ?? 0),
                'quotation_number' => $quotationVendor->requestQuotation->quotation_number,
            ];

            $result = $this->firebaseService->sendToTokensBatch(
                tokens: $tokens,
                title: $title,
                body: $body,
                imageUrl: null,
                data: $data
            );

            // Delete invalid tokens
            if (!empty($result['errors'])) {
                $invalidTokens = collect($result['errors'])->pluck('token')->toArray();
                CustomerFcmToken::where('customer_id', $customerId)
                    ->whereIn('fcm_token', $invalidTokens)
                    ->delete();
            }

        } catch (\Exception $e) {
            Log::error('Failed to send Firebase notification to customer: ' . $e->getMessage());
        }
    }

    /**
     * Send notification to admin when vendor sends offer
     */
    protected function notifyAdminOfferReceived(RequestQuotationVendor $quotationVendor): void
    {
        try {
            $customer = $quotationVendor->requestQuotation->customer;

            \App\Models\AdminNotification::notify(
                type: 'quotation_offer_received_admin',
                title: 'order::request-quotation.notification_admin_offer_title',
                description: 'order::request-quotation.notification_admin_offer_message',
                url: route('admin.request-quotations.view-offers', [
                    'lang' => app()->getLocale(),
                    'countryCode' => $quotationVendor->requestQuotation->country->code ?? 'eg',
                    'id' => $quotationVendor->request_quotation_id,
                ]),
                icon: 'uil-envelope-receive',
                color: 'info',
                notifiable: $quotationVendor,
                data: [
                    'vendor' => $quotationVendor->vendor->name,
                    'customer' => $customer->full_name ?? '-',
                    'price' => number_format($quotationVendor->offer_price ?? 0, 2) . ' ' . currency(),
                    'quotation_number' => $quotationVendor->requestQuotation->quotation_number,
                ],
                userId: null,
                vendorId: null
            );

        } catch (\Exception $e) {
            Log::error('Failed to send admin notification: ' . $e->getMessage());
        }
    }

    /**
     * Send notification when customer accepts vendor's offer
     */
    public function notifyVendorOfferAccepted(RequestQuotationVendor $quotationVendor): void
    {
        try {
            $vendor = $quotationVendor->vendor;
            $customer = $quotationVendor->requestQuotation->customer;

            if (!$vendor || !$customer) {
                return;
            }

            // Send notification to vendor
            \App\Models\AdminNotification::notify(
                type: 'vendor_offer_accepted',
                title: 'order::request-quotation.notification_vendor_offer_accepted_title',
                description: 'order::request-quotation.notification_vendor_offer_accepted_message',
                url: route('admin.vendor.request-quotations.show', [
                    'lang' => app()->getLocale(),
                    'countryCode' => $quotationVendor->requestQuotation->country->code ?? 'eg',
                    'id' => $quotationVendor->id,
                ]),
                icon: 'uil-check-circle',
                color: 'success',
                notifiable: $quotationVendor,
                data: [
                    'customer' => $customer->full_name,
                    'quotation_number' => $quotationVendor->requestQuotation->quotation_number,
                    'quotation_vendor_id' => $quotationVendor->id,
                ],
                userId: null,
                vendorId: $vendor->id
            );

        } catch (\Exception $e) {
            Log::error('Failed to send vendor offer accepted notification: ' . $e->getMessage());
        }
    }

    /**
     * Send notification when customer rejects vendor's offer
     */
    public function notifyVendorOfferRejected(RequestQuotationVendor $quotationVendor): void
    {
        try {
            $vendor = $quotationVendor->vendor;
            $customer = $quotationVendor->requestQuotation->customer;

            if (!$vendor || !$customer) {
                return;
            }

            // Send notification to vendor
            \App\Models\AdminNotification::notify(
                type: 'vendor_offer_rejected',
                title: 'order::request-quotation.notification_vendor_offer_rejected_title',
                description: 'order::request-quotation.notification_vendor_offer_rejected_message',
                url: route('admin.vendor.request-quotations.show', [
                    'lang' => app()->getLocale(),
                    'countryCode' => $quotationVendor->requestQuotation->country->code ?? 'eg',
                    'id' => $quotationVendor->id,
                ]),
                icon: 'uil-times-circle',
                color: 'danger',
                notifiable: $quotationVendor,
                data: [
                    'customer' => $customer->full_name,
                    'quotation_number' => $quotationVendor->requestQuotation->quotation_number,
                    'quotation_vendor_id' => $quotationVendor->id,
                ],
                userId: null,
                vendorId: $vendor->id
            );

        } catch (\Exception $e) {
            Log::error('Failed to send vendor offer rejected notification: ' . $e->getMessage());
        }
    }

    /**
     * Send notification when order is created from accepted offer
     */
    public function notifyCustomerOrderCreated(RequestQuotationVendor $quotationVendor): void
    {
        try {
            $customer = $quotationVendor->requestQuotation->customer;
            $order = $quotationVendor->order;

            if (!$customer || !$order) {
                return;
            }

            // Send AdminNotification to customer
            \App\Models\AdminNotification::notify(
                type: 'quotation_order_created',
                title: 'order::request-quotation.notification_customer_order_created_title',
                description: 'order::request-quotation.notification_customer_order_created_message',
                url: route('admin.orders.show', [
                    'lang' => app()->getLocale(),
                    'countryCode' => $quotationVendor->requestQuotation->country->code ?? 'eg',
                    'order' => $order->id,
                ]),
                icon: 'uil-shopping-cart',
                color: 'success',
                notifiable: $order,
                data: [
                    'vendor' => $quotationVendor->vendor->name,
                    'order_number' => $order->order_number,
                    'price' => number_format($order->total_price, 2) . ' ' . currency(),
                    'quotation_number' => $quotationVendor->requestQuotation->quotation_number,
                ],
                userId: $customer->id,
                vendorId: null
            );

            // Send Firebase notification to customer
            $this->sendFirebaseOrderCreated($customer->id, $quotationVendor, $order);

        } catch (\Exception $e) {
            Log::error('Failed to send order created notification: ' . $e->getMessage());
        }
    }

    /**
     * Send Firebase notification when order is created
     */
    protected function sendFirebaseOrderCreated(int $customerId, RequestQuotationVendor $quotationVendor, $order): void
    {
        try {
            $tokens = CustomerFcmToken::where('customer_id', $customerId)
                ->pluck('fcm_token')
                ->toArray();

            if (empty($tokens)) {
                return;
            }

            $title = __('order::request-quotation.notification_customer_order_created_title');
            $body = __('order::request-quotation.notification_customer_order_created_message', [
                'vendor' => $quotationVendor->vendor->name,
                'order_number' => $order->order_number,
            ]);

            $data = [
                'type' => 'quotation_order_created',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'quotation_id' => $quotationVendor->request_quotation_id,
                'vendor_name' => $quotationVendor->vendor->name,
                'price' => (string) $order->total_price,
            ];

            $result = $this->firebaseService->sendToTokensBatch(
                tokens: $tokens,
                title: $title,
                body: $body,
                imageUrl: null,
                data: $data
            );

            // Delete invalid tokens
            if (!empty($result['errors'])) {
                $invalidTokens = collect($result['errors'])->pluck('token')->toArray();
                CustomerFcmToken::where('customer_id', $customerId)
                    ->whereIn('fcm_token', $invalidTokens)
                    ->delete();
            }

        } catch (\Exception $e) {
            Log::error('Failed to send Firebase order created notification: ' . $e->getMessage());
        }
    }
}
