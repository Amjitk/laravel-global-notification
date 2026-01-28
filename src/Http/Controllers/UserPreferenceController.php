<?php

namespace AmjitK\GlobalNotification\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use AmjitK\GlobalNotification\Models\NotificationType;
use AmjitK\GlobalNotification\Models\UserPreference;

class UserPreferenceController extends Controller
{
    /**
     * Display the preference settings page.
     */
    public function index()
    {
        $types = NotificationType::with('templates')->get();

        // We only want to show types that have active templates
        // And generally we want to distinct by channel, but simpler to just show types and available channels.

        $user = Auth::user();
        $preferences = UserPreference::where('user_id', $user->id)->get();

        return view('global-notification::user.preferences.index', compact('types', 'preferences'));
    }

    /**
     * Update a specific preference.
     */
    public function update(Request $request)
    {
        $request->validate([
            'notification_type_id' => 'required|exists:gn_notification_types,id',
            'channel' => 'required|string',
            'is_enabled' => 'required|boolean',
        ]);

        $user = Auth::user();

        UserPreference::updateOrCreate(
            [
                'user_id' => $user->id,
                'notification_type_id' => $request->notification_type_id,
                'channel' => $request->channel,
            ],
            [
                'is_enabled' => $request->is_enabled
            ]
        );

        return response()->json(['success' => true]);
    }
}
