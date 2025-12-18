<?php

namespace AmjitK\GlobalNotification\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use AmjitK\GlobalNotification\Models\NotificationType;
use AmjitK\GlobalNotification\Models\NotificationTemplate;

class NotificationConfigController extends Controller
{
    public function index()
    {
        $types = NotificationType::with('templates')->get();
        return view('global-notification::admin.types.index', compact('types'));
    }

    public function create()
    {
        // View for creating type
        return view('global-notification::admin.types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:gn_notification_types,name',
            'description' => 'nullable|string'
        ]);

        $type = NotificationType::create($request->all());
        return redirect()->route('notification-types.index')->with('success', 'Notification Type Created');
    }
    
    public function show($id) {
         $type = NotificationType::with('templates')->findOrFail($id);
         return view('global-notification::admin.types.show', compact('type'));
    }

    // ... handle updates and template management here ...
    // For brevity, combining template management here or assumes separate calls
}
