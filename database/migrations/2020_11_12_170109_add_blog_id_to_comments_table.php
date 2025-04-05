<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBlogIdToCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->integer('blog_id')->after('webinar_id')->nullable()->unsigned();

            $table->foreign('reply_id')->references('id')->on('comments')->onDelete('cascade');
        });
    }
}
