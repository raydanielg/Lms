<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOnlineViewerColumnToFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->boolean('online_viewer')->default(false)->after('access_after_day');
        });

        Schema::table('product_files', function (Blueprint $table) {
            $table->string('file_type')->after('path')->nullable();
            $table->string('volume')->after('file_type')->nullable();
            $table->boolean('online_viewer')->default(false)->after('volume');
        });
    }
}
