<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
			$table->foreignId('reviewer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('assessment_date')->default(now());

            // 15 comments
            for ($i = 1; $i <= 15; $i++) {
                $table->text("comment_$i")->nullable();
            }

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};