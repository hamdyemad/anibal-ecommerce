<?php

namespace Modules\Order\app\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send WhatsApp message for new order
     * 
     * @param string $phone Customer phone number
     * @param string $orderNumber Order number
     * @param float $total Order total
     * @return bool Success status
     */
    public function sendOrderConfirmation(string $phone, string $orderNumber, float $total): bool
    {
        try {
            // Clean phone number (remove spaces, dashes, etc.)
            $phone = preg_replace('/[^0-9+]/', '', $phone);
            
            // Get locale for message
            $locale = app()->getLocale();
            
            // Build message based on locale
            if ($locale === 'ar') {
                $message = "شكراً لطلبك! 🎉\n\n";
                $message .= "رقم الطلب: {$orderNumber}\n";
                $message .= "المبلغ الإجمالي: " . number_format($total, 2) . " " . currency() . "\n\n";
                $message .= "سنقوم بمعالجة طلبك قريباً.\n";
                $message .= "يمكنك تتبع طلبك من خلال الرابط التالي:\n";
                $message .= url("/api/v1/orders/track/{$orderNumber}");
            } else {
                $message = "Thank you for your order! 🎉\n\n";
                $message .= "Order Number: {$orderNumber}\n";
                $message .= "Total Amount: " . number_format($total, 2) . " " . currency() . "\n\n";
                $message .= "We will process your order soon.\n";
                $message .= "Track your order here:\n";
                $message .= url("/api/v1/orders/track/{$orderNumber}");
            }
            
            // Check if WhatsApp API is configured
            $apiUrl = config('services.whatsapp.api_url');
            $apiToken = config('services.whatsapp.api_token');
            
            if (!$apiUrl || !$apiToken) {
                Log::warning('WhatsApp API not configured. Message not sent.', [
                    'phone' => $phone,
                    'order_number' => $orderNumber,
                ]);
                return false;
            }
            
            // Send WhatsApp message via API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
                'Content-Type' => 'application/json',
            ])->post($apiUrl, [
                'phone' => $phone,
                'message' => $message,
            ]);
            
            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'phone' => $phone,
                    'order_number' => $orderNumber,
                ]);
                return true;
            } else {
                Log::error('Failed to send WhatsApp message', [
                    'phone' => $phone,
                    'order_number' => $orderNumber,
                    'response' => $response->body(),
                ]);
                return false;
            }
            
        } catch (\Exception $e) {
            Log::error('WhatsApp service error: ' . $e->getMessage(), [
                'phone' => $phone,
                'order_number' => $orderNumber,
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }
}
