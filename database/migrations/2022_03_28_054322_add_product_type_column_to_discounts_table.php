<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddProductTypeColumnToDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('discounts', function (Blueprint $table) {
            $table->enum('product_type', ['all', 'physical', 'virtual'])->after('user_type')->nullable();

            DB::statement("ALTER TABLE `discounts` MODIFY COLUMN `source` enum('all', 'course', 'category', 'meeting', 'product') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `discount_type`");
        });
    }
}
