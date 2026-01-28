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
        Schema::table('gn_notification_types', function (Blueprint $table) {
            $table->json('variables')->nullable()->after('model_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gn_notification_types', function (Blueprint $table) {
            $table->dropColumn('variables');
        });
    }
};
