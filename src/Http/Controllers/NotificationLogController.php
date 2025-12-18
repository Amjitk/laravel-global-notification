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

        // Removed Auth::check() filtering to show global logs

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

        // Removed auth check constraints for global management
        $log->update(['read_at' => now()]);

        return back();
    }
}
