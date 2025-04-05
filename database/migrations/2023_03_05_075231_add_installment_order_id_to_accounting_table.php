<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstallmentOrderIdToAccountingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounting', function (Blueprint $table) {
            $table->integer('installment_order_id')->unsigned()->nullable()->after('installment_payment_id')->comment("This field is filled in the seller's financial document to find the installment order");
        });
    }
}
