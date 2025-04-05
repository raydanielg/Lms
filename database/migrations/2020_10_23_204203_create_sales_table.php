<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('order_id')->unsigned();
            $table->integer('webinar_id')->unsigned()->nullable();
            $table->integer('meeting_id')->unsigned()->nullable();
            $table->integer('meeting_time_id')->unsigned()->nullable();
            $table->integer('ticket_id')->unsigned()->nullable();
            $table->enum('type', ['webinar', 'meeting']);
            $table->enum('payment_method', ['credit', 'payment_channel']);
            $table->integer('amount')->unsigned();
            $table->integer('tax')->unsigned()->nullable();
            $table->integer('commission')->unsigned()->nullable();
            $table->integer('discount')->unsigned()->nullable();
            $table->integer('total_amount')->unsigned();
            $table->integer('created_at')->unsigned();
            $table->integer('refund_at')->unsigned()->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade');
            $table->foreign('meeting_id')->references('id')->on('meetings')->onDelete('cascade');
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
