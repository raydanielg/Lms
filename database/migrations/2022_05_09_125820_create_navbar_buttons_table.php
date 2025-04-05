<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNavbarButtonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('navbar_buttons', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('role_id')->unsigned()->nullable();
            $table->boolean('for_guest')->default(false);

            $table->foreign('role_id')->on('roles')->references('id')->cascadeOnDelete();
        });

        Schema::create('navbar_button_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->integer('navbar_button_id')->unsigned();
            $table->string('locale', 191)->index();
            $table->string('title');
            $table->string('url');

            $table->foreign('navbar_button_id')->on('navbar_buttons')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('navbar_buttons');
        Schema::dropIfExists('navbar_button_translations');
    }
}
