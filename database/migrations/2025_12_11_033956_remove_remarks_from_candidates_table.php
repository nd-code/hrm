<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('candidates', function (Blueprint $table) {
            if (Schema::hasColumn('candidates', 'remarks')) {
                $table->dropColumn('remarks');
            }
        });
    }

    public function down()
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->text('remarks')->nullable();
        });
    }
};