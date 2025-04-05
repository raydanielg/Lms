<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToSpecialOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('special_offers', function (Blueprint $table) {
            $table->integer('subscribe_id')->unsigned()->nullable()->after('bundle_id');
            $table->integer('registration_package_id')->unsigned()->nullable()->after('subscribe_id');

            $table->foreign('subscribe_id')->references('id')->on('subscribes')->onDelete('cascade');
            $table->foreign('registration_package_id')->references('id')->on('registration_packages')->onDelete('cascade');
        });
    }
}
