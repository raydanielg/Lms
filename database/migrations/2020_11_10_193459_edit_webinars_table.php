<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditWebinarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webinars', function (Blueprint $table) {
            DB::statement('ALTER TABLE `webinars` DROP COLUMN `start_time`,DROP COLUMN `end_time`');

            $table->integer('duration')->after('start_date')->unsigned();
            $table->boolean('downloadable')->after('support')->default(false);
        });
    }
}
