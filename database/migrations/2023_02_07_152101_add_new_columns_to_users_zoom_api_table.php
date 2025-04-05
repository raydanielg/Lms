<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToUsersZoomApiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_zoom_api', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `users_zoom_api` MODIFY COLUMN `jwt_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL AFTER `user_id`");

            $table->text('api_key')->after('jwt_token')->nullable();
            $table->text('api_secret')->after('api_key')->nullable();
            $table->text('account_id')->after('api_secret')->nullable();
        });
    }

}
