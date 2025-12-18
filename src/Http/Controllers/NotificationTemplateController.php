<?php

namespace AmjitK\GlobalNotification\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use AmjitK\GlobalNotification\Models\NotificationTemplate;

class NotificationTemplateController extends Controller
{
    public function index()
    {
        $templates = NotificationTemplate::with('type')->get();
        return view('global-notification::admin.templates.index', compact('templates'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'notification_type_id' => 'required|exists:gn_notification_types,id',
            'channel' => 'required|string',
            'content' => 'required|string',
        ]);

        NotificationTemplate::create($request->all());

        return back()->with('success', 'Template Created Successfully');
    }

    public function update(Request $request, $id)
    {
        $template = NotificationTemplate::findOrFail($id);
        $template->update($request->all());
        return back()->with('success', 'Template Updated');
    }

    public function destroy($id)
    {
        NotificationTemplate::destroy($id);
        return back()->with('success', 'Template Deleted');
    }
}
