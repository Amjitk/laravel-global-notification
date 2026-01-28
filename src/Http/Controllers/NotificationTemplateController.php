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
    public function test($id)
    {
        $template = NotificationTemplate::with('type')->findOrFail($id);
        $user = \Illuminate\Support\Facades\Auth::user();

        if (!$user) {
            return back()->with('error', 'You must be logged in to send a test.');
        }

        // Dummy data for variables
        $data = [];
        if ($template->type && $template->type->variables) {
            foreach ($template->type->variables as $var) {
                // Generate slightly more realistic dummy data based on var name?
                if (str_contains($var, 'id')) $data[$var] = '12345';
                elseif (str_contains($var, 'amount') || str_contains($var, 'price')) $data[$var] = '$99.00';
                elseif (str_contains($var, 'name')) $data[$var] = 'John Doe';
                elseif (str_contains($var, 'date')) $data[$var] = date('Y-m-d');
                else $data[$var] = '[' . strtoupper($var) . ']';
            }
        }

        // Use the Service helper
        $service = app(\AmjitK\GlobalNotification\Services\NotificationService::class);
        $result = $service->sendTemplate($user, $template, $data);

        if ($result) {
            return back()->with('success', 'Test notification sent to you via ' . $template->channel . '!');
        }

        return back()->with('error', 'Failed to send test notification.');
    }
}
