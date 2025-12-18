<?php

namespace AmjitK\GlobalNotification\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use AmjitK\GlobalNotification\Services\NotificationService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\DB;

class NotificationComposeController extends Controller
{
    protected $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    public function create()
    {
        $users = DB::table('users')->select('id', 'name', 'email')->limit(50)->get();

        return view('global-notification::admin.notifications.compose', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string',
            'content' => 'required|string',
            'recipient_type' => 'required|in:users,emails',
            'recipients' => 'nullable|required_if:recipient_type,users|array',
            'custom_emails' => 'nullable|required_if:recipient_type,emails|string',
            'channels' => 'required|array',
            'from_email' => 'nullable|email',
            'from_name' => 'nullable|string',
        ]);

        $channels = $request->input('channels');
        $subject = $request->input('subject');
        $content = $request->input('content');
        
        $data = [];
        if ($request->input('from_email')) {
            $data['from_email'] = $request->input('from_email');
            $data['from_name'] = $request->input('from_name');
        }

        if ($request->input('recipient_type') === 'emails') {
            // Process comma-separated emails
            $emails = array_map('trim', explode(',', $request->input('custom_emails')));
            
            \Illuminate\Support\Facades\Log::info('GlobalNotification: Sending manual to emails', ['count' => count($emails)]);

            foreach ($emails as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $guest = new \stdClass();
                    $guest->email = $email;
                    
                    $this->service->sendManual($guest, $subject, $content, $channels, $data);
                }
            }
        } else {
            $userClass = config('auth.providers.users.model', 'App\\Models\\User');
            $recipients = $request->input('recipients', []);

            \Illuminate\Support\Facades\Log::info('GlobalNotification: Sending manual to users', [
                'recipient_count' => count($recipients), 
                'user_model' => $userClass,
                'channels' => $channels
            ]);

            foreach ($recipients as $userId) {
                $user = $userClass::find($userId);
                if ($user) {
                    $this->service->sendManual($user, $subject, $content, $channels, $data);
                } else {
                    \Illuminate\Support\Facades\Log::warning("GlobalNotification: User not found", ['id' => $userId]);
                }
            }
        }

        return redirect()->route('global-notification.notifications.compose')
                         ->with('success', 'Notifications sent successfully!');
    }
}
