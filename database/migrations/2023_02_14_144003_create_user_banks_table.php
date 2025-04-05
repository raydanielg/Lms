<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_banks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('logo');
            $table->bigInteger('created_at')->unsigned();
        });

        Schema::create('user_bank_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_bank_id')->unsigned();
            $table->string('locale', 191)->index();
            $table->string('title');

            $table->foreign('user_bank_id')->on('user_banks')->references('id')->cascadeOnDelete();
        });

        Schema::create('user_bank_specifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_bank_id')->unsigned();

            $table->foreign('user_bank_id')->on('user_banks')->references('id')->cascadeOnDelete();
        });

        Schema::create('user_bank_specification_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_bank_specification_id')->unsigned();
            $table->string('locale', 191)->index();
            $table->string('name');

            $table->foreign('user_bank_specification_id', 'user_bank_specification_id')->on('user_bank_specifications')->references('id')->cascadeOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `users`
                    DROP COLUMN `account_type`,
                    DROP COLUMN `iban`,
                    DROP COLUMN `account_id`;");
        });

        Schema::create('user_selected_banks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('user_bank_id')->unsigned();

            $table->foreign('user_bank_id')->on('user_banks')->references('id')->cascadeOnDelete();
            $table->foreign('user_id')->on('users')->references('id')->cascadeOnDelete();
        });

        Schema::create('user_selected_bank_specifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_selected_bank_id')->unsigned();
            $table->integer('user_bank_specification_id')->unsigned();
            $table->text('value');

            $table->foreign('user_selected_bank_id', 'user_selected_bank_id_specifications')->on('user_selected_banks')->references('id')->cascadeOnDelete();
            $table->foreign('user_bank_specification_id', 'user_bank_specification_id_specifications')->on('user_bank_specifications')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_banks');
        Schema::dropIfExists('user_bank_translations');
        Schema::dropIfExists('user_bank_specifications');
        Schema::dropIfExists('user_bank_specification_translations');
        Schema::dropIfExists('user_selected_banks');
        Schema::dropIfExists('user_selected_bank_specifications');
    }
}
