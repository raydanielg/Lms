<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewslettersHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('newsletters_history', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->increments('id');
            $table->string('title');
            $table->longText('description');
            $table->enum('send_method', ['send_to_all', 'send_to_bcc', 'send_to_excel']);
            $table->string('bcc_email')->nullable();
            $table->integer('email_count')->nullable();
            $table->bigInteger('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('newsletters_history');
    }
}
