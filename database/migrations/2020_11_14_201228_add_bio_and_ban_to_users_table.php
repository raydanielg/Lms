<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBioAndBanToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('bio', '48')->nullable()->after('email');
            $table->boolean('ban')->default(false)->after('identity_scan');
            $table->integer('ban_start_at')->unsigned()->nullable()->after('ban');
            $table->integer('ban_end_at')->unsigned()->nullable()->after('ban_start_at');

            $table->integer('commission')->unsigned()->nullable()->after('identity_scan');

            DB::statement("ALTER TABLE `users` CHANGE COLUMN `identity` `verified` tinyint(1) NOT NULL DEFAULT 0 AFTER `remember_token`;");
        });
    }
}
