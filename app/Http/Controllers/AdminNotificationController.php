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
