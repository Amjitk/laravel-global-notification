<?php

namespace AmjitK\GlobalNotification\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use AmjitK\GlobalNotification\Models\NotificationLog;

class NotificationInAppController extends Controller
{
    /**
     * Get the latest unread notification for the authenticated user.
     * Used by the Toast widget.
     */
    public function latestUnread(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        // Fetch latest unread notification
        $notification = NotificationLog::where('notifiable_type', $user->getMorphClass())
            ->where('notifiable_id', $user->getKey())
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($notification) {
            // Check if this notification is "fresh" (e.g. created in last minute) 
            // OR simply return it and let frontend decide if it should be shown (by comparing IDs)
            return response()->json([
                'success' => true,
                'notification' => [
                    'id' => $notification->id,
                    'subject' => $notification->data['subject'] ?? 'New Notification',
                    'content' => \Illuminate\Support\Str::limit($notification->data['content'] ?? '', 100),
                    'created_at' => $notification->created_at->toIso8601String(),
                ]
            ]);
        }

        return response()->json(['success' => true, 'notification' => null]);
    }
}
