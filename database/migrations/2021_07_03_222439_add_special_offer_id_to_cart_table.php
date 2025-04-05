<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSpecialOfferIdToCartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart', function (Blueprint $table) {
            $table->integer('special_offer_id')->unsigned()->nullable()->after('ticket_id');

            $table->foreign('special_offer_id')->on('special_offers')->references('id')->onDelete('cascade');
        });
    }

}
