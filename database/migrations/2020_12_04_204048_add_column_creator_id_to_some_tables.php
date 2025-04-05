<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCreatorIdToSomeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->integer('creator_id')->unsigned()->after('id');
            $table->boolean('downloadable')->default(true)->after('accessibility')->unsigned();

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->integer('creator_id')->unsigned()->after('id');

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->integer('creator_id')->unsigned()->after('id');

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->integer('creator_id')->unsigned()->after('id');
            $table->integer('created_at')->unsigned()->nullable();
            $table->integer('updated_at')->unsigned()->nullable();

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('some_tables', function (Blueprint $table) {
            //
        });
    }
}
