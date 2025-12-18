<?php

namespace AmjitK\GlobalNotification\Http\Controllers;

use Illuminate\Routing\Controller;
use AmjitK\GlobalNotification\Models\NotificationLog;
use Illuminate\Http\Request;

class ScheduledNotificationController extends Controller
{
    public function index()
    {
        $logs = NotificationLog::query()
            ->where('meta->source', 'scheduled')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('global-notification::admin.notifications.scheduled', compact('logs'));
    }
}
