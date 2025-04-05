<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToAccountingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounting', function (Blueprint $table) {
            $table->boolean('is_registration_bonus')->after('is_affiliate_commission')->default(false);
            $table->integer('order_item_id')->after('creator_id')->unsigned()->nullable();
            $table->boolean('is_cashback')->default(false)->after('is_registration_bonus');
        });
    }
}
