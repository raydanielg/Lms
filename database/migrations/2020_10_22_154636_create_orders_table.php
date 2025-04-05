<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->enum('status', ['pending', 'paying', 'paid', 'fail']);
            $table->enum('payment_method', ['credit', 'payment_channel']);
            $table->integer('amount')->unsigned();
            $table->integer('tax')->unsigned()->nullable();
            $table->integer('total_discount')->unsigned()->nullable();
            $table->integer('total_amount')->unsigned();
            $table->integer('reference_id')->unsigned()->nullable();
            $table->integer('created_at')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
