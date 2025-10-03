<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Change id to uuid (Laravel expects UUIDs)
            $table->uuid('id')->change();

            if (!Schema::hasColumn('notifications', 'type')) {
                $table->string('type')->after('id');
            }

            if (!Schema::hasColumn('notifications', 'notifiable_type')) {
                $table->string('notifiable_type')->after('type');
            }

            if (!Schema::hasColumn('notifications', 'notifiable_id')) {
                $table->unsignedBigInteger('notifiable_id')->after('notifiable_type');
            }

            if (!Schema::hasColumn('notifications', 'data')) {
                $table->json('data')->after('notifiable_id');
            }

            if (!Schema::hasColumn('notifications', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('data');
            }

            // Drop old message column (we’ll store messages inside "data")
            if (Schema::hasColumn('notifications', 'message')) {
                $table->dropColumn('message');
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->text('message')->nullable();
            $table->dropColumn(['type', 'notifiable_type', 'notifiable_id', 'data', 'read_at']);
        });
    }
};