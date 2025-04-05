<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfflineBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offline_banks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('logo');
            $table->bigInteger('created_at')->unsigned();
        });

        Schema::create('offline_bank_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('offline_bank_id')->unsigned();
            $table->string('locale', 191)->index();
            $table->string('title');

            $table->foreign('offline_bank_id')->on('offline_banks')->references('id')->cascadeOnDelete();
        });

        Schema::create('offline_bank_specifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('offline_bank_id')->unsigned();
            $table->string('value');

            $table->foreign('offline_bank_id')->on('offline_banks')->references('id')->cascadeOnDelete();
        });

        Schema::create('offline_bank_specification_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('offline_bank_specification_id')->unsigned();
            $table->string('locale', 191)->index();
            $table->string('name');

            $table->foreign('offline_bank_specification_id', 'offline_bank_specification_id')->on('offline_bank_specifications')->references('id')->cascadeOnDelete();
        });

        Schema::table('offline_payments',function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `offline_payments` DROP COLUMN `bank`");

            $table->integer('offline_bank_id')->unsigned()->nullable()->after('amount');

            $table->foreign('offline_bank_id')->on('offline_banks')->references('id')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offline_banks_credits');
        Schema::dropIfExists('offline_bank_translations');
        Schema::dropIfExists('offline_bank_specifications');
        Schema::dropIfExists('offline_bank_specification_translations');
    }
}
