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
        Schema::table('employees', function (Blueprint $table) {
            $table->decimal('salary', 10, 2)->nullable()->after('bank_details');
            $table->decimal('tds', 10, 2)->nullable()->after('salary');
            $table->decimal('pt', 10, 2)->nullable()->after('tds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['salary', 'tds', 'pt']);
        });
    }
};
