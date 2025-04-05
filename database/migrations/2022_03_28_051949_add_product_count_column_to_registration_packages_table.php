<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductCountColumnToRegistrationPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registration_packages', function (Blueprint $table) {
            $table->integer('product_count')->unsigned()->after('meeting_count')->nullable();
        });
    }
}
