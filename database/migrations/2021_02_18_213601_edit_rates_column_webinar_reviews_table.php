<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditRatesColumnWebinarReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webinar_reviews', function (Blueprint $table) {
            DB::statement("ALTER TABLE `webinar_reviews` MODIFY COLUMN `rates` char(10) NOT NULL AFTER `support_quality`");
        });
    }
}
