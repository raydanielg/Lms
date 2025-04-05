<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forums', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->string('slug')->unique()->index();
            $table->integer('role_id')->unsigned()->nullable();
            $table->integer('group_id')->unsigned()->nullable();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->string('icon')->nullable();
            $table->enum('status', ['disabled', 'active'])->nullable();
            $table->boolean('close')->default(false);
            $table->integer('order')->nullable();

            $table->foreign('role_id')->on('roles')->references('id')->onDelete('cascade');
            $table->foreign('group_id')->on('groups')->references('id')->onDelete('cascade');
        });

        Schema::create('forum_translations', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');
            $table->unsignedInteger('forum_id');
            $table->string('locale', 191)->index();
            $table->string('title');
            $table->text('description')->nullable();

            $table->foreign('forum_id')->on('forums')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forums');
        Schema::dropIfExists('forum_translations');
    }
}
