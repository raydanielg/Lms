<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckSequenceContentFieldsToContentsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->boolean('check_previous_parts')->default(false)->after('agora_settings');
            $table->integer('access_after_day')->unsigned()->nullable()->after('check_previous_parts');
        });

        Schema::table('files', function (Blueprint $table) {
            $table->boolean('check_previous_parts')->default(false)->after('interactive_file_path');
            $table->integer('access_after_day')->unsigned()->nullable()->after('check_previous_parts');
        });

        Schema::table('text_lessons', function (Blueprint $table) {
            $table->boolean('check_previous_parts')->default(false)->after('accessibility');
            $table->integer('access_after_day')->unsigned()->nullable()->after('check_previous_parts');
        });

        Schema::table('webinar_chapters', function (Blueprint $table) {
            $table->boolean('check_all_contents_pass')->default(false)->after('order');
        });
    }
}
