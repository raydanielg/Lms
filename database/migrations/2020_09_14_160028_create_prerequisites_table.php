<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrerequisitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prerequisites', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('webinar_id')->unsigned();
            $table->integer('prerequisite_id')->unsigned();
            $table->boolean('required')->default(false);
            $table->integer('created_at');
            $table->integer('updated_at')->nullable();

            $table->foreign('webinar_id')->references('id')->on('webinars')->onDelete('cascade');;
            $table->foreign('prerequisite_id')->references('id')->on('webinars')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prerequisites');
    }
}
