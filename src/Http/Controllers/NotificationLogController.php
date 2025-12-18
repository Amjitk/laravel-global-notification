<?php

namespace AmjitK\GlobalNotification\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use AmjitK\GlobalNotification\Models\NotificationLog;
use Illuminate\Support\Facades\Auth;

class NotificationLogController extends Controller
{
    public function index(Request $request)
    {
        $query = NotificationLog::query();

        if (Auth::check()) {
            $query->where('notifiable_id', Auth::id())
                  ->where('notifiable_type', Auth::user()->getMorphClass());
        }

        // Apply filters (Universal)
        if ($request->has('read')) {
            if ($request->read == 'true') {
                 $query->whereNotNull('read_at');
            } elseif ($request->read == 'false') {
                 $query->whereNull('read_at');
            }
        }

        $query->orderBy('created_at', 'desc');

        $notifications = $query->paginate(20);
        
        return view('global-notification::user.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $log = NotificationLog::findOrFail($id);
        
        if (!Auth::check() || $log->notifiable_id == Auth::id()) {
            $log->update(['read_at' => now()]);
        }
        
        return back();
    }
}
