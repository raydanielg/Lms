<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBecomeInstructorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('become_instructors', function (Blueprint $table) {
            $table->engine = 'InnoBD';

            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('certificate')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'accept', 'reject'])->default('pending');
            $table->integer('created_at')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('become_instructors');
    }
}
