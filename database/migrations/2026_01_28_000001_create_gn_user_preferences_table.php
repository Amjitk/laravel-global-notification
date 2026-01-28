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
        Schema::create('gn_user_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // We assume standard user_id for simplicity, but could be morph if needed. Sticking to user_id as per plan.
            // Actually, the plan mentioned "morph/unsignedBigInteger". The package seems to support 'notifiable'.
            // However, preferences usually belong to a standard "User". Let's stick to user_id for now as it's the 99% use case.
            // If the package uses morph logic elsewhere heavily, I might reconsider, but `UserPreference` usually implies the auth user.

            $table->unsignedBigInteger('notification_type_id');
            $table->string('channel'); // mail, database, etc.
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->foreign('notification_type_id')->references('id')->on('gn_notification_types')->onDelete('cascade');

            // Unique constraint to prevent duplicates
            $table->unique(['user_id', 'notification_type_id', 'channel'], 'user_pref_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gn_user_preferences');
    }
};
