<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->enum('type', [\App\Models\Reward::getTypesLists()]);
            $table->integer('score')->unsigned()->nullable();
            $table->string('condition')->nullable();
            $table->enum('status', ['active', 'disabled']);
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
        Schema::dropIfExists('rewards');
    }
}
