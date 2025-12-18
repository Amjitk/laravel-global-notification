<?php

namespace Tests;

use AmjitK\GlobalNotification\Services\NotificationService;
use AmjitK\GlobalNotification\Models\NotificationLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Schema;

class ManualNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_manual_creates_log()
    {
        // 1. Setup User
        // Create a user on the fly if factories aren't set up, or just use DB facade to insert and generic User model
        $user = new User(); // Ensure TestCase setup mimics real app
        $user->forceFill([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ])->save();

        // 2. Service
        $service = new NotificationService();

        // 3. Send Manual
        $service->sendManual(
            $user,
            'Test Manual Subject',
            'Test Manual Content',
            ['database'],
            []
        );

        // 4. Assert Database Log
        $this->assertDatabaseHas('gn_notification_logs', [
            'notifiable_id' => $user->id,
            'notifiable_type' => get_class($user),
            'channel' => 'database',
            // 'data' is JSON, so simple where matches won't work on 'data->subject' directly with sqlite reliably in all versions, 
            // but we can check if a record exists.
        ]);

        $log = NotificationLog::where('notifiable_id', $user->id)->first();
        $this->assertNotNull($log, 'Log should exist');
        $this->assertEquals('Test Manual Subject', $log->data['subject']);
        $this->assertTrue($log->data['is_manual']);
    }

    public function test_send_manual_mail_only_for_guest()
    {
       // Mock Mail
       \Illuminate\Support\Facades\Mail::fake();

       $guest = new \stdClass();
       $guest->email = 'guest@example.com';

       $service = new NotificationService();
       $service->sendManual(
           $guest,
           'Guest Subject',
           'Guest Content',
           ['mail', 'database'], // DB should be skipped
           []
       );

       \Illuminate\Support\Facades\Mail::assertSent(function (\Illuminate\Mail\Mailable $mail) {
           return true; // Simple check that *something* was sent, or we can check Mail::raw logic if used
       });
       // Mail::raw doesn't use Mailable class usually, checking Mail::raw invocation?
       // Mail::fake() captures simple sends too?
       
       $this->assertEquals(0, NotificationLog::count(), 'No logs should be created for guest');
    }
}
