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
            'description' => 'nullable|string',
            'variables' => 'nullable|string', // Comma separated
        ]);

        $data = $request->all();
        if ($request->variables) {
            $data['variables'] = array_map('trim', explode(',', $request->variables));
        }

        $type = NotificationType::create($data);
        return redirect()->route('global-notification.notification-types.index')->with('success', 'Notification Type Created');
    }

    public function show($id)
    {
        $type = NotificationType::with('templates')->findOrFail($id);
        return view('global-notification::admin.types.show', compact('type'));
    }

    public function edit($id)
    {
        $type = NotificationType::findOrFail($id);
        return view('global-notification::admin.types.edit', compact('type'));
    }

    public function update(Request $request, $id)
    {
        $type = NotificationType::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:gn_notification_types,name,' . $type->id,
            'description' => 'nullable|string',
            'variables' => 'nullable|string',
        ]);

        $data = $request->except(['_token', '_method']);

        if ($request->has('variables')) {
            $data['variables'] = $request->variables
                ? array_map('trim', explode(',', $request->variables))
                : null;
        }

        $type->update($data);
        return redirect()->route('global-notification.notification-types.index')->with('success', 'Notification Type Updated');
    }

    public function destroy($id)
    {
        NotificationType::findOrFail($id)->delete();
        return redirect()->route('global-notification.notification-types.index')->with('success', 'Notification Type Deleted');
    }
}
