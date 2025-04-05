<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderColumnToWebinarItemsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->integer('order')->unsigned()->nullable()->after('capacity');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->integer('order')->unsigned()->nullable()->after('description');
        });

        Schema::table('files', function (Blueprint $table) {
            $table->integer('order')->unsigned()->nullable()->after('description');
        });

        Schema::table('text_lessons', function (Blueprint $table) {
            $table->integer('order')->unsigned()->nullable()->after('accessibility');
        });

        Schema::table('prerequisites', function (Blueprint $table) {
            $table->integer('order')->unsigned()->nullable()->after('required');
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->integer('order')->unsigned()->nullable()->after('answer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('webinar_items_tables', function (Blueprint $table) {
            //
        });
    }
}
