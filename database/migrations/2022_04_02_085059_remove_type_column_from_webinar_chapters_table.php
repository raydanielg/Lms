<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveTypeColumnFromWebinarChaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webinar_chapters', function (Blueprint $table) {
            DB::statement("ALTER TABLE `webinar_chapters` DROP COLUMN `type`");
        });
    }
}
