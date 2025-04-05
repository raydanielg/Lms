<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSaleIdInSubscribeUseInSubscribeUsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscribe_uses', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('sale_id')->unsigned();

            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscribe_uses', function (Blueprint $table) {
            $table->dropColumn('sale_id');
        });
    }
}
