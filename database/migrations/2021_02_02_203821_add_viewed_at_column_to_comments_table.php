<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddViewedAtColumnToCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            DB::statement("ALTER TABLE `comments` MODIFY COLUMN `created_at` int(11) NOT NULL AFTER `disabled`");
            $table->integer('viewed_at')->unsigned()->nullable();
        });
    }
}
