<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("
            ALTER TABLE candidates 
            MODIFY status ENUM('pending','on_hold','selected','rejected') 
            DEFAULT 'pending'
        ");
    }

    public function down()
    {
        DB::statement("
            ALTER TABLE candidates 
            MODIFY status ENUM('pending','selected','rejected') 
            DEFAULT 'pending'
        ");
    }
};
