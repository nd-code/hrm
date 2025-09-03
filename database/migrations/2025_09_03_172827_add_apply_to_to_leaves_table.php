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
		Schema::table('leaves', function (Blueprint $table) {
			$table->string('apply_to')->nullable()->after('employee_id'); // Comma-separated IDs
		});
	}

	public function down(): void
	{
		Schema::table('leaves', function (Blueprint $table) {
			$table->dropColumn('apply_to');
		});
	}
};
