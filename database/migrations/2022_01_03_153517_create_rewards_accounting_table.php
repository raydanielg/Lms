<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRewardsAccountingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rewards_accounting', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('item_id')->unsigned()->nullable();
            $table->enum('type', array_merge(\App\Models\Reward::getTypesLists(), ['withdraw']));
            $table->integer('score')->unsigned();
            $table->enum('status', ['addiction', 'deduction']);
            $table->bigInteger('created_at')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rewards_accounting');
    }
}
