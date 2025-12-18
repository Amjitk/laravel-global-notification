<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gn_notification_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 'order_placed', 'user_registered'
            $table->string('description')->nullable();
            $table->string('model_type')->nullable(); // Optional: to link to specific models
            $table->timestamps();
        });

        Schema::create('gn_notification_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_type_id')->constrained('gn_notification_types')->onDelete('cascade');
            $table->string('channel'); // email, database, sms, push
            $table->string('subject')->nullable(); // For email/push title
            $table->text('content'); // Supports rich text / variables
            $table->json('configurations')->nullable(); // Extra config like icon, color, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('gn_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notifiable_id'); // User ID or other entity
            $table->string('notifiable_type');
            $table->foreignId('notification_type_id')->constrained('gn_notification_types')->onDelete('cascade');
            $table->string('channel');
            $table->text('data')->nullable(); // The actual message sent
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gn_notification_logs');
        Schema::dropIfExists('gn_notification_templates');
        Schema::dropIfExists('gn_notification_types');
    }
};
