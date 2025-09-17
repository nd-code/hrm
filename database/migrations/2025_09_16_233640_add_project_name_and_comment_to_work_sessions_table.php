<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
	{
		Schema::table('work_sessions', function (Blueprint $table) {
			$table->string('project_name')->nullable()->after('end_time');
			$table->text('comment')->nullable()->after('project_name');
		});
	}

	public function down()
	{
		Schema::table('work_sessions', function (Blueprint $table) {
			$table->dropColumn(['project_name', 'comment']);
		});
	}
};
