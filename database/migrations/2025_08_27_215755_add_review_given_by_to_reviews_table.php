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
		Schema::table('reviews', function (Blueprint $table) {
			$table->string('review_given_by')->nullable()->after('review'); // Add the field
		});
	}

	public function down(): void
	{
		Schema::table('reviews', function (Blueprint $table) {
			$table->dropColumn('review_given_by');
		});
	}
};
