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
        Schema::create('teams', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            // Manager (parent employee)
            $table->unsignedBigInteger('parent_employee_id');
            
            // Team member (employee)
            $table->unsignedBigInteger('employee_id');
            
            $table->timestamps();

            // Foreign keys
            $table->foreign('parent_employee_id')
                  ->references('id')
                  ->on('employees')
                  ->onDelete('cascade');

            $table->foreign('employee_id')
                  ->references('id')
                  ->on('employees')
                  ->onDelete('cascade');
            
            // Optional: prevent duplicate entries for same manager-employee pair
            $table->unique(['parent_employee_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};