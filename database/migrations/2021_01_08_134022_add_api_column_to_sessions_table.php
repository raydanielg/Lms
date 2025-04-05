<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApiColumnToSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sessions', function (Blueprint $table) {
            DB::statement("ALTER TABLE `sessions` MODIFY COLUMN `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL AFTER `duration`");

            $table->enum('session_api', \App\Models\Session::$sessionApis)->default('local')->after('link');
            $table->string('api_secret')->after('session_api')->nullable();
        });
    }
}
