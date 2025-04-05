<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \Illuminate\Support\Facades\DB;

class EditColumnInDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('discounts', function (Blueprint $table) {
            DB::statement("ALTER TABLE `discounts` CHANGE COLUMN `type` `user_type` enum('all_users','special_users') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `count`");

            $table->string('subtitle')->nullable()->after('title');
            $table->enum('discount_type', ['percentage', 'fixed_amount'])->after('subtitle');
            $table->enum('source', \App\Models\Discount::$discountSource)->after('discount_type');
            $table->integer('max_amount')->nullable()->unsigned()->after('amount');
            $table->integer('minimum_order')->nullable()->unsigned()->after('max_amount');
            $table->boolean('for_first_purchase')->default(false)->after('user_type');
            $table->boolean('private')->default(false)->after('for_first_purchase');
        });
    }
}
