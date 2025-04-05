<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip_restrictions', function (Blueprint $table) {
            $table->increments('id');
            $table->enum("type", ['full_ip', 'ip_range', 'country']);
            $table->string('value')->comment("full ip or ip range or country name");
            $table->text('reason');
            $table->bigInteger("created_at")->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ip_restrictions');
    }
};
