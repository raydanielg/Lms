<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAffiliateColumnToAccountingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounting', function (Blueprint $table) {
            $table->integer('referred_user_id')->unsigned()->nullable()->after('store_type');
            $table->boolean('is_affiliate_amount')->after('affiliate_user_id')->default(false);
            $table->boolean('is_affiliate_commission')->after('is_affiliate_amount')->default(false);
        });
    }
}
