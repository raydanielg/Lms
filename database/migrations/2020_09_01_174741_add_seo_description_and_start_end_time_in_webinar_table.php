<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSeoDescriptionAndStartEndTimeInWebinarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webinars', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->dropColumn('end_date');
            $table->integer('start_time')->after('start_date');
            $table->integer('end_time')->after('start_time');
            $table->string('seo_description',128)->nullable()->after('end_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('webinars', function (Blueprint $table) {
            $table->integer('end_date');
            $table->dropColumn('start_time');
            $table->dropColumn('end_time');
            $table->dropColumn('seo_description');
        });
    }
}
