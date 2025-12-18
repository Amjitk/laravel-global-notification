<?php

namespace AmjitK\GlobalNotification\Tests;

use AmjitK\GlobalNotification\Models\NotificationType;
use AmjitK\GlobalNotification\Models\NotificationTemplate;
use AmjitK\GlobalNotification\Services\NotificationService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Schema;

class NotificationServiceTest extends TestCase
{
    /** @test */
    public function it_can_send_a_notification()
    {
        // 1. Create a Type
        $type = NotificationType::create([
            'name' => 'test_event',
            'description' => 'Test Description'
        ]);

        // 2. Create a Template
        NotificationTemplate::create([
            'notification_type_id' => $type->id,
            'channel' => 'database',
            'subject' => 'Hello {{name}}',
            'content' => 'Welcome {{name}} to our platform.',
            'is_active' => true
        ]);
        
        $user = new class extends \Illuminate\Database\Eloquent\Model {
            protected $table = 'users';
            protected $guarded = [];
        };
        
        // Ensure users table exists for this test if needed, or just mock getKey()
        Schema::create('users', function($table) {
            $table->id();
            $table->string('email');
            $table->timestamps();
        });
        
        $user = $user->create(['email' => 'test@example.com']);

        // 4. Send Notification
        $service = new NotificationService();
        $service->send('test_event', $user, ['name' => 'John']);

        // 5. Assert Log Created
        $this->assertDatabaseHas('gn_notification_logs', [
            'notifiable_id' => $user->id,
            'channel' => 'database',
            'data' => json_encode([
                'subject' => 'Hello John',
                'content' => 'Welcome John to our platform.',
                'original_data' => ['name' => 'John']
            ])
        ]);
    }
}
