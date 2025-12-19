<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->text('comment')->nullable()->after('interview_date');
        });

        // copy old data
        \DB::statement("UPDATE candidates SET comment = feedback");

        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn('feedback');
        });
    }

    public function down()
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->text('feedback')->nullable();
        });

        \DB::statement("UPDATE candidates SET feedback = comment");

        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn('comment');
        });
    }
};