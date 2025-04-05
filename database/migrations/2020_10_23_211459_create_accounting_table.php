<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounting', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('creator_id')->nullable();
            $table->integer('webinar_id')->unsigned()->nullable();
            $table->integer('meeting_id')->unsigned()->nullable();
            $table->boolean('system')->default(0);
            $table->boolean('tax')->default(0);
            $table->integer('amount');
            $table->enum('type', ['addiction', 'deduction']);
            $table->enum('type_account', ['income', 'asset']);
            $table->text('description')->nullable();
            $table->integer('created_at');

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounting');
    }
}
