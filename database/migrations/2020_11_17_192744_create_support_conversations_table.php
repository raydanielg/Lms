<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupportConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_conversations', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('support_id')->unsigned();
            $table->integer('supporter_id')->unsigned()->nullable();
            $table->integer('sender_id')->unsigned()->nullable();
            $table->string('attach')->nullable();
            $table->text('message');
            $table->integer('created_at')->unsigned();

            $table->foreign('support_id')->references('id')->on('supports')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('supporter_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('support_conversations');
    }
}
