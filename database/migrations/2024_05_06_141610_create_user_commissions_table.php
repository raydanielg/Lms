<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_commissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('user_group_id')->unsigned()->nullable();
            $table->enum('source', \App\Models\UserCommission::$sources);
            $table->enum('type', ['percent', 'fixed_amount']);
            $table->float('value', 15, 2)->nullable();

            $table->foreign('user_id')->on('users')->references('id')->cascadeOnDelete();
            $table->foreign('user_group_id')->on('groups')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_commissions');
    }
};
