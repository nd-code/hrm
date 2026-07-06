<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('holidays', function (Blueprint $table) {
            $table->dropColumn('is_optional');
            $table->string('day', 20)->after('holiday_date');
        });
    }

    public function down(): void
    {
        Schema::table('holidays', function (Blueprint $table) {
            $table->dropColumn('day');
            $table->boolean('is_optional')->default(false)->after('holiday_date');
        });
    }
};
