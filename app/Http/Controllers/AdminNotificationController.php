<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Services\AdminNotificationService;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function __construct(
        protected AdminNotificationService $notificationService
    ) {}

    /**
     * Show notification details
     */
    public function show($lang, $countryCode, $id)
    {
        $notification = AdminNotification::with('notifiable')->findOrFail($id);
        
        // Mark as viewed by current user
        $this->notificationService->markAsViewedBy($id, auth()->id());
        return view('notifications.show', compact('notification'));
    }

    /**
     * Get paginated notifications (for infinite scroll)
     * Optimized for performance
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = 10;
        $type = $request->get('type'); // Optional type filter
        
        $userId = auth()->id();
        $isAdmin = isAdmin();
        
        // Build base query with optimized scope
        $query = AdminNotification::notViewedBy($userId)
            ->orderBy('admin_notifications.created_at', 'desc'); // Specify table name after JOIN
        
        // Filter by type if provided
        if ($type) {
            $query->where('admin_notifications.type', $type);
        }
        
        // Optimize vendor/admin filtering
        if ($isAdmin) {
            // Admin sees: notifications without vendor_id OR refund-related notifications
            $query->where(function($q) {
                $q->whereNull('admin_notifications.vendor_id')
                  ->orWhere('admin_notifications.type', 'new_refund_request')
                  ->orWhere('admin_notifications.type', 'refund_status_changed');
            });
        } else {
            // Vendor sees: their notifications OR global notifications (excluding admin-only types)
            $vendorId = auth()->user()->vendor->id;
            
            $query->where(function($q) use ($vendorId) {
                $q->where('admin_notifications.vendor_id', $vendorId)
                  ->orWhereNull('admin_notifications.vendor_id');
            })
            ->whereNotIn('admin_notifications.type', ['vendor_request', 'new_message']);
        }
        
        // Get paginated results
        $notifications = $query->paginate($perPage);
        
        // Map to array
        $items = $notifications->map(function($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'icon' => $notification->icon,
                'color' => $notification->color,
                'title' => $notification->getTranslatedTitle(),
                'description' => $notification->getTranslatedDescription(),
                'url' => route('admin.notifications.show', [
                    'lang' => app()->getLocale(), 
                    'countryCode' => strtolower(session('country_code', 'eg')), 
                    'id' => $notification->id
                ]),
                'created_at' => $notification->getRawOriginal('created_at'),
            ];
        });
        
        return response()->json([
            'notifications' => $items,
            'current_page' => $notifications->currentPage(),
            'last_page' => $notifications->lastPage(),
            'total' => $notifications->total(),
            'has_more' => $notifications->hasMorePages(),
        ]);
    }

    /**
     * Get unread notifications count
     * Optimized for performance
     */
    public function count()
    {
        $userId = auth()->id();
        $isAdmin = isAdmin();
        
        // Build optimized query
        $query = AdminNotification::notViewedBy($userId);
        
        // Optimize vendor/admin filtering
        if ($isAdmin) {
            $query->where(function($q) {
                $q->whereNull('admin_notifications.vendor_id')
                  ->orWhere('admin_notifications.type', 'new_refund_request')
                  ->orWhere('admin_notifications.type', 'refund_status_changed');
            });
        } else {
            $vendorId = auth()->user()->vendor->id;
            $query->where(function($q) use ($vendorId) {
                $q->where('admin_notifications.vendor_id', $vendorId)
                  ->orWhereNull('admin_notifications.vendor_id');
            })
            ->whereNotIn('admin_notifications.type', ['vendor_request', 'new_message']);
        }
        
        $count = $query->count();
        
        return response()->json(['count' => $count]);
    }

    public function markAsRead(Request $request)
    {
        $notification = AdminNotification::find($request->id);
        
        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        AdminNotification::unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
