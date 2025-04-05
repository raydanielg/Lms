<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChapterIdToFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->integer('chapter_id')->after('webinar_id')->nullable()->unsigned();
            $table->enum('status', \App\Models\File::$fileStatus)->after('order')->default('active');

            $table->foreign('chapter_id')->on('webinar_chapters')->references('id')->onDelete('cascade');
        });
    }
}
