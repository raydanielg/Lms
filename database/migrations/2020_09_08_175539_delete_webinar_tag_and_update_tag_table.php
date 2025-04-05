<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteWebinarTagAndUpdateTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('webinar_tag');

        Schema::table('tags', function (Blueprint $table) {
            $table->integer('item_id')->unsigned();
            $table->enum('type',['webinar']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn('item_id');
            $table->dropColumn('type');
        });
    }
}
