<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJazzcashTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jazzcash_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('txn_ref_no');
            $table->json('order')->comment('Order data fields and values');
            $table->json('request')->comment('Jazzcash request data fields and values');
            $table->json('response')->nullable()->comment('Jazzcash response data fields and values');
            $table->enum('status', ['pending', 'error', 'completed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jazzcash_transactions');
    }
}
