<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \Illuminate\Support\Facades\DB;

class EditDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('discounts', function (Blueprint $table) {
            DB::statement("ALTER TABLE `discounts` DROP COLUMN `name`");
            DB::statement("ALTER TABLE `discount_users` DROP COLUMN `count`");
            DB::statement("ALTER TABLE `discounts` DROP COLUMN `started_at`, MODIFY COLUMN `created_at` int(0) UNSIGNED NOT NULL AFTER `expired_at`;");

            $table->string('title')->after('creator_id');
            $table->string('code', 64)->after('title')->unique();
            $table->enum('type', ['all_users', 'special_users'])->after('count');
        });
    }
}
